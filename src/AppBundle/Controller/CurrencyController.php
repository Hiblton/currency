<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Goutte\Client;
use Symfony\Component\HttpKernel\Exception\HttpException;

use AppBundle\Entity\ExchangeRate;

class CurrencyController extends Controller
{
    /**
     * @param string $date
     * @Route("/show/{date}", name="currency")
     * @throws
     */
    public function showAction($date)
    {
        if (!$date) {
            $date = new \DateTime();
        } else {
            $date = \DateTime::createFromFormat("Y-m-d", $date);
            //if date is in future, set today date
            if (new \DateTime() < $date) {
                $date = new \DateTime();
            }
        }
        $rateList = $this->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->getExchangeRatesByDate($date->format('Y-m-d'));
        $currencyList = $this->getDoctrine()
            ->getRepository('AppBundle:Currency')
            ->getAllCurrencies();

        //check if we have no data on this date or added new currency
        if (!$rateList || count($currencyList) !== count($rateList)) {
            //clear all rows
            $em = $this->getDoctrine()->getManager();
            foreach ($rateList as $item) {
                $em->remove($item);
            }
            $em->flush();
            //create client for grabbing
            $client = new Client();
            try {
                $crawler = $client->request('GET', 'http://www.nbrb.by/Services/XmlExRates.aspx?ondate=' . $date->format('m/d/Y'));
            } catch (\Exception $e) {
                $this->get('mail_helper')->sendEmail('from@support.com', 'to@admin.com', $e);
                throw new HttpException(500, "Whoops! Something was wrong. :/");
            }
            $crawler->filter('Currency')->each(function ($node) use ($date, $em) {
                $exist_currency = $this->getDoctrine()
                    ->getRepository('AppBundle:Currency')
                    ->findOneBy(array('code' => $node->filter('CharCode')->text()));
                if ($exist_currency) {
                    $item = new ExchangeRate();
                    $item->setDate($date->format('Y-m-d'));
                    $item->setCode($node->filter('CharCode')->text());
                    $item->setPrice($node->filter('Rate')->text());

                    $em->persist($item);
                }
            });
            $em->flush();

            $rateList = $this->getDoctrine()
                ->getRepository('AppBundle:ExchangeRate')
                ->getExchangeRatesByDate($date->format('Y-m-d'));
        }

        return $this->render('currency/index.html.twig', array('rateList' => $rateList));
    }
}

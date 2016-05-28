<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Goutte\Client;

use AppBundle\Entity\ExchangeRate;

class CurrencyController extends Controller
{
    /**
     * @param string $date
     * @Route("/show/{date}", name="currency")
     */
    public function showAction($date)
    {
        if (!$date) {
            $date = new \DateTime();
        } else {
            $date = \DateTime::createFromFormat("Y-m-d", $date);
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
            $crawler = $client->request('GET', 'http://www.nbrb.by/statistics/rates/ratesdaily.asp?date=' . $date->format('Y-m-d'));
            $crawler->filter('.stexttbl tr')->each(function ($node) use ($date, $em) {
                if ($node->filter('td')->count()) {
                    $exist_currency = $this->getDoctrine()
                        ->getRepository('AppBundle:Currency')
                        ->findOneBy(array('code' => $node->filter('td')->first()->text()));
                    if ($exist_currency) {
                        $item = new ExchangeRate();
                        $item->setDate($date->format('Y-m-d'));
                        $item->setCode($exist_currency->getCode());
                        $item->setPrice($node->filter('td')->last()->text());

                        $em->persist($item);
                    }
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

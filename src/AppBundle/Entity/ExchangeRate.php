<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="exchange_rate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExchangeRate")
 */
class ExchangeRate
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="date", type="string")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=10)
     *
     * @ORM\OneToMany(targetEntity="Currency", mappedBy="currency")
     * @ORM\JoinColumn(name="code", referencedColumnName="code")
     */
    private $code;

    /**
     * @ORM\Column(type="string")
     */
    private $price;

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ExchangeRate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ExchangeRate
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return ExchangeRate
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}

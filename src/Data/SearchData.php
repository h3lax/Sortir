<?php

namespace App\Data;

use App\Entity\Campus;
use Symfony\Component\Validator\Constraints\DateTime;

class SearchData
{
    /**
     * @var Campus
     */
    public $campus ;

    /**
     * @var null|string
     */
    public $recherche ='';

    /**
     * @var null|DateTime
     */
    public $debutPeriode = null;

    /**
     * @var null|DateTime
     */
    public $finPeriode = null;

    /**
     * @var bool
     */
    public $organisateur = false;

    /**
     * @var bool
     */
    public $inscrit = false;

    /**
     * @var bool
     */
    public $pasInscrit = false;

    /**
     * @var bool
     */
    public $past = false;

}
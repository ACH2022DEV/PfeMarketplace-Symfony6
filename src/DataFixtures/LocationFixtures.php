<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Continent;
use App\Entity\Country;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $continents = array(
            array('code' => 'AF','name' => 'Africa','active' => '1'),
            array('code' => 'AN','name' => 'Antarctica','active' => '0'),
            array('code' => 'AS','name' => 'Asia','active' => '0'),
            array('code' => 'EU','name' => 'Europe','active' => '0'),
            array('code' => 'NA','name' => 'North America','active' => '0'),
            array('code' => 'OC','name' => 'Oceania','active' => '0'),
            array('code' => 'SA','name' => 'South America','active' => '0')
        );
        
        $countries = array(
            array('code' => 'DZ','alpha3' => 'DZA','name' => 'Algérie','phone_code' => '213','currency_code' => 'DZD','capital' => 'Algiers','continent_code' => 'AF','active' => '0'),
            array('code' => 'AD','alpha3' => 'AND','name' => 'Andorre','phone_code' => '376','currency_code' => 'EUR','capital' => 'Andorra la Vella','continent_code' => 'EU','active' => '0'),
            array('code' => 'BE','alpha3' => 'BEL','name' => 'Belgique','phone_code' => '32','currency_code' => 'EUR','capital' => 'Brussels','continent_code' => 'EU','active' => '0'),
            array('code' => 'CA','alpha3' => 'CAN','name' => 'Canada','phone_code' => '1','currency_code' => 'CAD','capital' => 'Ottawa','continent_code' => 'NA','active' => '0'),
            array('code' => 'EG','alpha3' => 'EGY','name' => 'Egypte','phone_code' => '20','currency_code' => 'EGP','capital' => 'Cairo','continent_code' => 'AF','active' => '0'),
            array('code' => 'FR','alpha3' => 'FRA','name' => 'France','phone_code' => '33','currency_code' => 'EUR','capital' => 'Paris','continent_code' => 'EU','active' => '0'),
            array('code' => 'DE','alpha3' => 'DEU','name' => 'Allemagne','phone_code' => '49','currency_code' => 'EUR','capital' => 'Berlin','continent_code' => 'EU','active' => '0'),
            array('code' => 'IT','alpha3' => 'ITA','name' => 'Italie','phone_code' => '39','currency_code' => 'EUR','capital' => 'Rome','continent_code' => 'EU','active' => '0'),
            array('code' => 'JP','alpha3' => 'JPN','name' => 'Japon','phone_code' => '81','currency_code' => 'JPY','capital' => 'Tokyo','continent_code' => 'AS','active' => '0'),
            array('code' => 'LB','alpha3' => 'LBN','name' => 'Liban','phone_code' => '961','currency_code' => 'LBP','capital' => 'Beirut','continent_code' => 'AS','active' => '0'),
            array('code' => 'MV','alpha3' => 'MDV','name' => 'Maldives','phone_code' => '960','currency_code' => 'MVR','capital' => 'Male','continent_code' => 'AS','active' => '0'),
            array('code' => 'MT','alpha3' => 'MLT','name' => 'Malte','phone_code' => '356','currency_code' => 'EUR','capital' => 'Valletta','continent_code' => 'EU','active' => '0'),
            array('code' => 'MC','alpha3' => 'MCO','name' => 'Monaco','phone_code' => '377','currency_code' => 'EUR','capital' => 'Monaco','continent_code' => 'EU','active' => '0'),
            array('code' => 'MA','alpha3' => 'MAR','name' => 'Maroc','phone_code' => '212','currency_code' => 'MAD','capital' => 'Rabat','continent_code' => 'AF','active' => '0'),
            array('code' => 'NL','alpha3' => 'NLD','name' => 'Pays-Bas','phone_code' => '31','currency_code' => 'EUR','capital' => 'Amsterdam','continent_code' => 'EU','active' => '0'),
            array('code' => 'PT','alpha3' => 'PRT','name' => 'Portugal','phone_code' => '351','currency_code' => 'EUR','capital' => 'Lisbon','continent_code' => 'EU','active' => '0'),
            array('code' => 'QA','alpha3' => 'QAT','name' => 'Qatar','phone_code' => '974','currency_code' => 'QAR','capital' => 'Doha','continent_code' => 'AS','active' => '0'),
            array('code' => 'SA','alpha3' => 'SAU','name' => 'Arabie Saoudite','phone_code' => '966','currency_code' => 'SAR','capital' => 'Riyadh','continent_code' => 'AS','active' => '0'),
            array('code' => 'ES','alpha3' => 'ESP','name' => 'Espagne','phone_code' => '34','currency_code' => 'EUR','capital' => 'Madrid','continent_code' => 'EU','active' => '0'),
            array('code' => 'TN','alpha3' => 'TUN','name' => 'Tunisie','phone_code' => '216','currency_code' => 'TND','capital' => 'Tunis','continent_code' => 'AF','active' => '0'),
            array('code' => 'AE','alpha3' => 'ARE','name' => 'Emirats Arabes Unis','phone_code' => '971','currency_code' => 'AED','capital' => 'Abu Dhabi','continent_code' => 'AS','active' => '0'),
            array('code' => 'GB','alpha3' => 'GBR','name' => 'Royaume-Uni','phone_code' => '44','currency_code' => 'GBP','capital' => 'London','continent_code' => 'EU','active' => '0'),
            array('code' => 'US','alpha3' => 'USA','name' => 'États-Unis','phone_code' => '1','currency_code' => 'USD','capital' => 'Washington','continent_code' => 'NA','active' => '0'),
        );

        $cities = array(
            array('country_code' => 'TN','name' => 'Bizerte','latitude' => '37.274420000000','longitude' => '9.873910000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Carthage','latitude' => '36.859610000000','longitude' => '10.329780000000','active' => '1'),
            array('country_code' => 'TN','name' => 'El Jem','latitude' => '35.300000000000','longitude' => '10.716670000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Gabès','latitude' => '33.881460000000','longitude' => '10.098200000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Mahdia','latitude' => '35.504720000000','longitude' => '11.062220000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Monastir','latitude' => '35.777990000000','longitude' => '10.826170000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Sfax','latitude' => '34.740560000000','longitude' => '10.760280000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Hammamet','latitude' => '36.400512','longitude' => '10.613140','active' => '1'),
            array('country_code' => 'TN','name' => 'Sousse','latitude' => '35.825390000000','longitude' => '10.636990000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Tabarka','latitude' => '36.954420000000','longitude' => '8.758010000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Tozeur','latitude' => '33.919680000000','longitude' => '8.133520000000','active' => '1'),
            array('country_code' => 'TN','name' => 'Tunis','latitude' => '36.818970000000','longitude' => '10.165790000000','active' => '1'),
            array('country_code' => 'FR','name' => 'Paris','latitude' => '48.856614','longitude' => '2.3522219','active' => '1')
        );


        foreach ($continents as $c){
            $continent = new Continent();
            $continent->setCode($c['code']);
            $continent->setName($c['name']);
            $continent->setActive($c['active']);
            $this->addReference('Continent_'.$c['code'], $continent);
            $manager->persist($continent);
        }

        foreach ($countries as $c){
            $country = new Country();
            $country->setCode($c['code']);
            $country->setName($c['name']);
            $country->setPhoneCode($c['phone_code']);
            $country->setCapital($c['capital']);
            $country->setAlpha3($c['alpha3']);
            $country->setActive(true);
            $country->setCurrency(($this->getReference('Currency_'.$c['currency_code'])));
            $country->setContinent(($this->getReference('Continent_'.$c['continent_code'])));
            $this->addReference('Country_'.$c['code'], $country);
            $manager->persist($country);
        }


        foreach ($cities as $c){
            $city = new City();
            $city->setName($c['name']);
            $city->setActive(true);
            $city->setLatitude($c['latitude']);
            $city->setLongitude($c['longitude']);
            $city->setCountryCode(($this->getReference('Country_'.$c['country_code'])));
            $this->addReference('City_'.$c['name'], $city);
            $manager->persist($city);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            CurrencyFixtures::class,
        ];
    }
}
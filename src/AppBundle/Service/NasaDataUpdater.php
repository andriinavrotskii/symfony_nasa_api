<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 23.11.17
 * Time: 17:23
 */

namespace AppBundle\Service;

use AppBundle\Entity\Neo;
use AppBundle\ExternalApi\Nasa\NasaException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;

class NasaDataUpdater
{
    /**
     * Couter for NEOs data
     * @var int
     */
    protected $countAll;

    /**
     * Counter for NEOs data saved in DB
     * @var int
     */
    protected $countNew;

    /**
     * @instance EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $data
     * @return array
     */
    public function update(array $data)
    {
        $this->countAll = 0;
        $this->countNew = 0;

        $this->goTrougthNasaData($data);

        return [
            'new' => $this->countNew,
            'all' => $this->countAll,
        ];
    }

    /**
     * @param array $data
     * @throws NasaException
     * @return void
     */
    protected function goTrougthNasaData(array $data)
    {
        try {
            foreach ($data as $date => $dayData) {
                foreach ($dayData as $neoData) {
                    $this->saveToDbAndCount([
                        'date' => $date,
                        'reference' => $neoData->neo_reference_id,
                        'name' => $neoData->name,
                        'speed' => $neoData->close_approach_data[0]->relative_velocity->kilometers_per_hour,
                        'is_hazardous' => $neoData->is_potentially_hazardous_asteroid,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            throw new NasaException("Error: NASA Data is not valid. " . $e->getMessage());
        }
    }

    /**
     * @param array $neoData
     * @throws NasaException
     * @return void
     */
    protected function saveToDbAndCount(array $data)
    {
        try {
            $neo = $this->em->getRepository('AppBundle:Neo')->findOneBy(['reference' => $data['reference']]);

            if (!$neo) {
                $neo = new Neo();
                $this->countNew++;
            }
            $this->countAll++;

            $neo->setName($data['name']);
            $neo->setIsHazardous($data['is_hazardous'] == 'true' ? true : false);
            $neo->setSpeed((float) $data['speed']);
            $neo->setReference((int) $data['reference']);
            $neo->setDate(\DateTime::createFromFormat('Y-m-d', $data['date']));

            $this->em->persist($neo);
            $this->em->flush();

        } catch (\Throwable $e) {
            throw new NasaException("Error: Update DB" . $e->getMessage());
        }
    }
}
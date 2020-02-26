<?php


namespace App\EventListener;

use App\Repository\CoursRepository;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarListener
{
    private $coursRepository;
    private $router;

    public function __construct(
        CoursRepository $coursRepository,
        UrlGeneratorInterface $router
    ) {
        $this->coursRepository = $coursRepository;
        $this->router = $router;
    }

    public function EnseignantLoad(CalendarEvent $calendar): void
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();


        $cours = $this->coursRepository
            ->createQueryBuilder('cour')
            ->where('cour.beginAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($cours as $cour) {
            // this create the events with your data (here booking data) to fill calendar
            $courEvent = new Event(
                $cour->getTitre(),
                $cour->getBeginAt(),
                $cour->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $courEvent->setOptions([
                'backgroundColor' => 'green',
                'borderColor' => 'green',
            ]);
            $courEvent->addOption(
                'url',
                $this->router->generate('cours_show', [
                    'id' => $cour->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($courEvent);
        }
    }
    public function EtudiantLoad(CalendarEvent $calendar): void
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        $cours = $this->coursRepository
            ->createQueryBuilder('cour')
            ->where('cour.beginAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($cours as $cour) {
            // this create the events with your data (here booking data) to fill calendar
            $courEvent = new Event(
                $cour->getTitre(),
                $cour->getBeginAt(),
                $cour->getEndAt() // If the end date is null or not defined, a all day event is created.
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */

            $courEvent->setOptions([
                'backgroundColor' => 'green',
                'borderColor' => 'green',
            ]);
            $courEvent->addOption(
                'url',
                $this->router->generate('cours_show', [
                    'id' => $cour->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($courEvent);
        }
    }
}
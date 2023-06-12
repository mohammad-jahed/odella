<?php

namespace App\Enums;

class NotificationType
{
    //Employee
    const Registration = 0;

    //Student
    const ExpiredSubscription = 1;
    const PositionTime = 2;
    const ReturnTime = 3;
    const StopRegistration = 4;

    //Supervisor
    const SupervisorDailyReservation = 5;

    //Guest
    const GuestDailyReservation = 6;

}

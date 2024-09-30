<?php


enum BillingCycle: string
{
    case MONTHLY = 'Monthly';
    case QUARTERLY = 'Quarterly';
    case SEMI_ANNUALLY = 'SemiAnnually';
    case ANNUALLY = 'Annually';
    case BIENNIALLY = 'Biennially';
    case ONE_TIME = 'OneTime';
}

enum DomainRegistrationPeriod: string
{
    case ONE_YEAR = '1 Year';
    case TWO_YEARS = '2 Years';
    case THREE_YEARS = '3 Years';
    
}

<?php

namespace TrustPay\Enums;

class CardTransactionType
{
    const CARD_ON_FILE_REGISTRATION = 1;
    const CARD_ON_FILE = 2;
    const INITIAL_TRANSACTION = 3;
    const SUBSEQUENT_RECURRING = 4;
    const AUTHORIZATION_STANDARD_PAYMENT = 5;
    const CAPTURE = 6;
    const REFUND = 8;
    const AUTHORIZATION_CARD_ON_FILE = 9;
}

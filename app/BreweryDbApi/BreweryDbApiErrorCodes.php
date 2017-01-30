<?php
namespace App\BreweryDbApi;

interface BreweryDbApiErrorCodes
{
    /**
     * No brewery ID was provided
     */
    const ERR_BREWERY_ID      = 1;
    /**
     * Search pattern is empty
     */
    const ERR_SEARCH_EMPTY    = 2;
    /**
     * Search type different than accepted
     */
    const ERR_SEARCH_TYPE     = 3;
    /**
     * All the allowed API requests were consumed
     */
    const ERR_REQUEST_LIMIT   = 4;
}
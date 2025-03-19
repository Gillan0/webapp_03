<?php

namespace App\Entity;

/**
 * Represents a way to sort {@link Item} in a {@link Wishlist}
 */
enum SortOrder
{
    /**
     * Sort by price in ascending order 
     */
    case PriceAscending;

    /**
     * Sort by price in descending order 
     */
    case PriceDescending;

}


?>
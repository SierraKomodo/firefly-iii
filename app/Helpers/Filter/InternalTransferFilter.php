<?php
/**
 * InternalTransferFilter.php
 * Copyright (c) 2017 thegrumpydictator@gmail.com
 * This software may be modified and distributed under the terms of the Creative Commons Attribution-ShareAlike 4.0 International License.
 *
 * See the LICENSE file for details.
 */

declare(strict_types=1);

namespace FireflyIII\Helpers\Filter;

use FireflyIII\Models\Transaction;
use Illuminate\Support\Collection;
use Log;

/**
 * Class InternalTransferFilter
 *
 * This filter removes any filters that are from A to B or from B to A given a set of
 * account id's (in $parameters) where A and B are mentioned. So transfers between the mentioned
 * accounts will be removed.
 *
 * @package FireflyIII\Helpers\Filter
 */
class InternalTransferFilter implements FilterInterface
{

    /**
     * @param Collection $set
     * @param null       $parameters
     *
     * @return Collection
     */
    public function filter(Collection $set, $parameters = null): Collection
    {
        return $set->filter(
            function (Transaction $transaction) use ($parameters) {
                if (is_null($transaction->opposing_account_id)) {
                    return $transaction;
                }
                // both id's in $parameters?
                if (in_array($transaction->account_id, $parameters) && in_array($transaction->opposing_account_id, $parameters)) {
                    Log::debug(
                        sprintf(
                            'Transaction #%d has #%d and #%d in set, so removed',
                            $transaction->id, $transaction->account_id, $transaction->opposing_account_id
                        ), $parameters
                    );

                    return false;
                }

                return $transaction;

            }
        );


    }
}
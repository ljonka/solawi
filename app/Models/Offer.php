<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property float amount
 * @property int round
 * @property User user
 * @property BidderRound bidderRound
 */
class Offer extends BaseModel
{
    use HasFactory;

    public const TABLE = 'offer';

    protected $table = self::TABLE;

    public const COL_ID = 'id';
    public const COL_AMOUNT = 'amount';
    public const COL_ROUND = 'round';
    public const COL_FK_BIDDER_ROUND = 'fkBidderRound';
    public const COL_FK_USER = 'fkUser';

    protected $fillable = [
        self::COL_ID,
        self::COL_AMOUNT,
        self::COL_ROUND,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::COL_FK_USER);
    }

    public function bidderRound(): BelongsTo
    {
        return $this->belongsTo(BidderRound::class, self::COL_FK_BIDDER_ROUND);
    }

    public function isInputStillPossible(): bool
    {
        if (!$this->bidderRound) {
            // Should never happen
            return false;
        }

        return $this->bidderRound->isOfferStillPossible();
    }
}

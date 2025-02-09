<?php

namespace Tests\Feature;

use App\Http\Livewire\BidderRoundForm;
use App\Models\BidderRound;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Those tests make sure the bidder round form is working properly for creating and editing a {@link BidderRound}.
 */
class BidderRoundFormTest extends TestCase
{
    /**
     * This tests checks the creation of a bidder round and the corresponding validations.
     */
    public function testCreateBidderRound()
    {
        $this->createUser();

        Livewire::test(BidderRoundForm::class)
            ->call('save')
            ->assertHasErrors();

        Livewire::test(BidderRoundForm::class)
            ->set('validFrom', '2022-01-01T13:22:21+01:00')
            ->set('validTo', '2022-12-31T13:22:21+01:00')
            ->set('startOfSubmission', '2022-03-01T13:22:21+01:00')
            ->set('endOfSubmission', '2022-03-15T13:22:21+01:00')
            ->set('bidderRound.countOffers', 4)
            ->set('bidderRound.targetAmount', 68_000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseCount(BidderRound::class, 1);

        /** @var BidderRound $bidderRound */
        $bidderRound = BidderRound::query()->first();

        $this->assertTrue($bidderRound->validFrom->isSameDay(Carbon::createFromFormat('Y-m-d', '2022-01-01')));
        $this->assertTrue($bidderRound->validTo->isSameDay(Carbon::createFromFormat('Y-m-d', '2022-12-31')));
        $this->assertTrue($bidderRound->startOfSubmission->isSameDay(Carbon::createFromFormat('Y-m-d', '2022-03-01')));
        $this->assertTrue($bidderRound->endOfSubmission->isSameDay(Carbon::createFromFormat('Y-m-d', '2022-03-15')));
        $this->assertEquals(4, $bidderRound->countOffers);
        $this->assertEquals(68_000, $bidderRound->targetAmount);
    }

    /**
     * This test checks if there is a bidder round route available for a fresh created round.
     */
    public function testEditExistingBidderRound()
    {
        $this->createUser();

        /** @var BidderRound $bidderRound */
        $bidderRound = BidderRound::query()->create([
            BidderRound::COL_VALID_FROM => Carbon::createFromFormat('Y-m-d', '2022-01-01'),
            BidderRound::COL_VALID_TO => Carbon::createFromFormat('Y-m-d', '2022-12-31'),
            BidderRound::COL_START_OF_SUBMISSION => Carbon::createFromFormat('Y-m-d', '2022-03-01'),
            BidderRound::COL_END_OF_SUBMISSION => Carbon::createFromFormat('Y-m-d', '2022-03-15'),
            BidderRound::COL_TARGET_AMOUNT => 68_000,
            BidderRound::COL_COUNT_OFFERS => 5,
        ]);

        $this->get("bidderRounds/$bidderRound->id")->assertSeeLivewire('bidder-round-form');
    }
}

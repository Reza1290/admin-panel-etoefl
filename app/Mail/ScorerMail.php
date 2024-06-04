<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScorerMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $initPacketName;
    public $akurasiProsentase;
    public $scoreToefl;
    public $targetUser;
    public $answerUser;
    public $correctQuestionAll;
    public $totalQuestionAll;
    public $correctQuestionListeningAll;
    public $totalQuestionListeningAll;
    public $listeningPartACorrect;
    public $totalQuestionListeningPartA;
    public $accuracyListeningPartA;
    public $correctQuestionListeningPartB;
    public $totalQuestionListeningPartB;
    public $accuracyListeningPartB;
    public $correctQuestionListeningPartC;
    public $totalQuestionListeningPartC;
    public $accuracyListeningPartC;
    public $correctQuestionStructureAll;
    public $totalQuestionStructureAll;
    public $correctQuestionStructurePartA;
    public $totalQuestionStructurePartA;
    public $accuracyStructurePartA;
    public $correctQuestionStructurePartB;
    public $totalQuestionStructurePartB;
    public $accuracyStructurePartB;
    public $correctQuestionReading;
    public $totalQuestionReading;
    public $accuracyReading;
    public $userScoreInformation;

    public function __construct($initPacketName, $akurasiProsentase, $scoreToefl, $targetUser, $answerUser, $correctQuestionAll, $totalQuestionAll, $correctQuestionListeningAll, $totalQuestionListeningAll, $listeningPartACorrect, $totalQuestionListeningPartA, $accuracyListeningPartA, $correctQuestionListeningPartB, $totalQuestionListeningPartB, $accuracyListeningPartB, $correctQuestionListeningPartC, $totalQuestionListeningPartC, $accuracyListeningPartC, $correctQuestionStructureAll, $totalQuestionStructureAll, $correctQuestionStructurePartA, $totalQuestionStructurePartA, $accuracyStructurePartA, $correctQuestionStructurePartB, $totalQuestionStructurePartB, $accuracyStructurePartB, $correctQuestionReading, $totalQuestionReading, $accuracyReading, $userScoreInformation)
    {
        $this->initPacketName = $initPacketName;
        $this->akurasiProsentase = $akurasiProsentase;
        $this->scoreToefl = $scoreToefl;
        $this->targetUser = $targetUser;
        $this->answerUser = $answerUser;
        $this->correctQuestionAll = $correctQuestionAll;
        $this->totalQuestionAll = $totalQuestionAll;
        $this->correctQuestionListeningAll = $correctQuestionListeningAll;
        $this->totalQuestionListeningAll = $totalQuestionListeningAll;
        $this->listeningPartACorrect = $listeningPartACorrect;
        $this->totalQuestionListeningPartA = $totalQuestionListeningPartA;
        $this->accuracyListeningPartA = $accuracyListeningPartA;
        $this->correctQuestionListeningPartB = $correctQuestionListeningPartB;
        $this->totalQuestionListeningPartB = $totalQuestionListeningPartB;
        $this->accuracyListeningPartB = $accuracyListeningPartB;
        $this->correctQuestionListeningPartC = $correctQuestionListeningPartC;
        $this->totalQuestionListeningPartC = $totalQuestionListeningPartC;
        $this->accuracyListeningPartC = $accuracyListeningPartC;
        $this->correctQuestionStructureAll = $correctQuestionStructureAll;
        $this->totalQuestionStructureAll = $totalQuestionStructureAll;
        $this->correctQuestionStructurePartA = $correctQuestionStructurePartA;
        $this->totalQuestionStructurePartA = $totalQuestionStructurePartA;
        $this->accuracyStructurePartA = $accuracyStructurePartA;
        $this->correctQuestionStructurePartB = $correctQuestionStructurePartB;
        $this->totalQuestionStructurePartB = $totalQuestionStructurePartB;
        $this->accuracyStructurePartB = $accuracyStructurePartB;
        $this->correctQuestionReading = $correctQuestionReading;
        $this->totalQuestionReading = $totalQuestionReading;
        $this->accuracyReading = $accuracyReading;
        $this->userScoreInformation = $userScoreInformation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Scorer TOEFL Test : ' . $this->scoreToefl . " - " . $this->userScoreInformation,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.score',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Enums;

enum OrderStatus: int
{ 
    case PROCESSING = 1;
    case AGREED = 2;
    case COMPLETED = 3;
    case RESEND_PREVIOUS = 4;
    case RESEND_CREATOR = 5;
    case STOPPED = 6;

    public function isProcessing(): bool
    {
        return $this === self::PROCESSING;
    }

    public function isAgreed(): bool
    {
        return $this === self::AGREED;
    }


    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isResendPrevious(): bool
    {
        return $this === self::RESEND_PREVIOUS;
    }

    public function isResendCreator(): bool
    {
        return $this === self::RESEND_CREATOR;
    }

    public function isStopped(): bool
    {
        return $this === self::STOPPED;
    }


    public static function toArray(): array
    {
        return [
            self::PROCESSING->value,
            self::AGREED->value,
            self::COMPLETED->value,
            self::RESEND_PREVIOUS->value,
            self::RESEND_CREATOR->value,
            self::STOPPED->value
        ];
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PROCESSING => trans('text.In Processing'),
            self::AGREED => trans('text.Agreed'),
            self::COMPLETED => trans('text.Completed'),
            self::RESEND_PREVIOUS => trans('text.Resend Previous'),
            self::RESEND_CREATOR => trans('test.Resend Creator'),
            self::STOPPED => trans('text.Stopped'),
        };
    }
}

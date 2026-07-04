<?php

class StackVerifyClient
{
    public static function send($formId, $data)
    {
        if (empty($formId)) {
            return;
        }

        return wp_remote_post(
            "https://stackverify.site/api/f/" . urlencode($formId),
            [
                'method'  => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body'    => json_encode($data),
            ]
        );
    }
}

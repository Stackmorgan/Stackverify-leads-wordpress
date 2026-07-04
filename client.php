class StackVerifyClient {

    public static function send($formId, $data) {

        if (!$formId) return;

        wp_remote_post(
            "https://stackverify.site/api/f/" . $formId,
            [
                'method'  => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ]
        );
    }
}

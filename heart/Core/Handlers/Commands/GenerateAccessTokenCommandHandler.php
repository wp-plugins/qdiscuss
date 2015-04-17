<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Models\AccessToken;

class GenerateAccessTokenCommandHandler
{
    public function handle($command)
    {
        $token = AccessToken::generate($command->userId);
        $token->save();

        return $token;
    }
}

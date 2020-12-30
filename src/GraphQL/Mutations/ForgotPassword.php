<?php

namespace Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Password;
use Joselfonseca\LighthouseGraphQLPassport\Events\ForgotPasswordRequested;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ForgotPassword
{

    /**
     * @param $rootValue
     * @param array                                                    $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext|null $context
     * @param \GraphQL\Type\Definition\ResolveInfo                     $resolveInfo
     *
     * @throws \Exception
     *
     * @return array
     */
    public function resolve($rootValue, array $args, GraphQLContext $context = null, ResolveInfo $resolveInfo)
    {
        $response = $this->broker()->sendResetLink(['email' => $args['email']]);
        if ($response == Password::RESET_LINK_SENT) {
            event(new ForgotPasswordRequested($args['email']));

            return [
                'status'  => 'EMAIL_SENT',
                'message' => __($response),
            ];
        }

        return [
            'status'  => 'EMAIL_NOT_SENT',
            'message' => __($response),
        ];
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}

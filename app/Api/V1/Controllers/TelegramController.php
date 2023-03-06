<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\PendingRequest;
use Log;
use Telegram\Bot\Api;
use Illuminate\Http\Request;
use Telegram\Bot\Exceptions\TelegramResponseException;

class TelegramController extends Controller
{
    //
    /**
     * Subscribe a user to the chat bot
     *
     * @OA\Get(
     *     path="/api/V1/subscribe",
     *     summary="Subscribe a user to the chat bot",
     *     tags={"Subscription"},
     *     @SWG\Parameter(
     *         name="user-id",
     *         in="header",
     *         description="User ID of the user to be subscribed",
     *         required=true,
     *         type="integer"
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="User subscribed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="null"),
     *             @OA\Property(property="data", type="string",  example="You have been subscribed to our chat bot"),
     *             @OA\Property(property="message", type="string", example="User subscribed successfully"),
     *             @OA\Property(property="status", type="string", example="success"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="An error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *
     *
     *         )
     *     )
     * )
     */
    public function subscribeToBot(Request $request,Api $telegram){
        try{
            $user_id = $request->header('user-id');
            if($user_id){
                return $this->successResponse('User subscribed successfully', 'You have been subscribed to our chat bot');
            }
        } catch (TelegramResponseException $e) {
            return $this->errorResponse('An error occurred', $e->getMessage(), 400);
        }
    }

    /**
     * Subscribe user to a Telegram channel or group.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Telegram\Bot\Api $telegram
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/api/V1/subscribe-to-channel",
     *     tags={"Subscription"},
     *     summary="Subscribe user to a Telegram channel or group",
     *     operationId="subscribeToChannel",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="user-id",
     *         in="header",
     *         description="User ID of the user to be subscribed",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="User subscribed successfully to channel",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="error", type="string", example="null"),
     *             @SWG\Property(property="data", type="string",  example="You have been subscribed to our channel"),
     *             @SWG\Property(property="message", type="string", example="User subscribed successfully to channel"),
     *             @SWG\Property(property="status", type="string", example="success")
     *
     *         )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="An error occurred",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function subscribeToChannel(Request $request,Api $telegram) {
        // Add user to the channel or group
        try{
            $chatId = $request->header('user-id');
            $response = $telegram->getMe();
            $channelUsername = $response->getUsername();
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Please join our Telegram channel at https://t.me/'.$channelUsername.' to receive updates.'
            ]);
            return $this->successResponse('User subscribed successfully to channel', 'You have been subscribed to our channel');
        } catch (TelegramResponseException $e) {
            return $this->errorResponse('An error occurred', $e->getMessage(), 400);
        }
    }

    /**
     * Sends a message to a specified chat ID using the Telegram API.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Telegram\Bot\Api $telegram
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/api/V1/send-message",
     *     summary="Send a message to a chat ID",
     *     tags={"Telegram"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="chat_id",
     *         in="query",
     *         description="The ID of the chat to send the message to",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="query",
     *         description="The message to send",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="error", type="string", example="null"),
     *             @SWG\Property(property="data", type="string",  example="Message Sent"),
     *             @SWG\Property(property="message", type="string", example="Message sent successfully"),
     *             @SWG\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="An error occurred",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="error",
     *                 type="string",
     *                 example="The error message"
     *             )
     *         )
     *     )
     * )
     */
    public function sendMessage(Request $request, Api $telegram){
        try{
            $chatId = $request->input('chat_id');
            $message = $request->input('message');
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message
            ]);
            return $this->successResponse('Message sent successfully', 'Message Sent');
        } catch (TelegramResponseException $e) {
            return $this->errorResponse('An error occurred', $e->getMessage(), 400);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/V1/setwebhook",
     *     summary="Set webhook for Telegram bot",
     *     description="Sets a webhook for a Telegram bot using the specified token.",
     *     operationId="setWebhook",
     *     tags={"Telegram"},
     *     @OA\RequestBody(
     *         description="JSON payload containing the token",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="webhook_url", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function setWebhook(Request $request,Api $telegram)
    {
        try {
            $token = $request->input('token');
            $url = url('/webhook');
            $telegram->setWebhook(['url' => "https://api.telegram.org/$token/setWebhook?url=$url"]);
            $response = ['token' => $token, 'webhook_url' => $url];
            return $this->successResponse('WebHook Succesfully Created', $response);
        } catch (\Throwable $th) {
            return $this->errorResponse('An error occurred', $th->getMessage(), 400);
        }
    }
    /**
     * @OA\Get(
     *     path="path="/api/V1/webhook",
     *     summary="Handle webhook data",
     *     description="Handles the data received from the webhook and sends it to the specified URL.",
     *     operationId="handleWebhook",
     *     tags={"Webhook"},
     *     @OA\RequestBody(
     *         description="JSON payload containing the webhook data",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     * )
     */


    public function handleWebhook(Request $request,$requestData){
        $webhook_url = $request->header('webhook_url');
        $client = new PendingRequest();
        $client->post($webhook_url, $requestData);
        return $this->successResponse('Succesfully Sent', 'Succesfully Sent');
    }

}

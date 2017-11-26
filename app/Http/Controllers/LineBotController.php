<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\UserEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\BaseEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Log;

class LineBotController extends Controller
{
  private $replyToken;
  private $bot;
  private $userId;
  private $roomId;
  private $groupId;
  private $lineSourceType;
  private $displayName;
  private $pictureUrl;

  public function __invoke(ServerRequestInterface $req){
      
    $signature = $req->getHeader('X-Line-Signature');
    
    if (empty($signature)) {
      return $res->withStatus(400, 'Bad Request');
    }
    $this->bot = resolve('LINE\LINEBot');
    Log::info("Get Request");

    // Check request with signature and parse request
    try {
        $events = $this->bot->parseEventRequest($req->getBody(), $signature[0]);
    } catch (InvalidSignatureException $e) {
      return $res->withStatus(400, 'Invalid signature');
    } catch (InvalidEventRequestException $e) {
      return $res->withStatus(400, "Invalid event request");
    }

    $jsonObj = json_decode($req->getBody());
    //如果有userId
    if(property_exists($jsonObj->{"events"}[0]->{"source"},'userId')){
        $this->userId=$jsonObj->{"events"}[0]->{"source"}->{"userId"};
    }

    foreach ($events as $event) {

      if($event instanceof JoinEvent){
          Log::info("JoinEvent");
          continue;
      }
      if($event instanceof LeaveEvent){
          Log::info("LeaveEvent");
          continue;
      }
      if($event instanceof UserEvent){
          Log::info("UserEvent");
          continue;
      }

      if (!($event instanceof MessageEvent)) {
        Log::info('Non message event has come');
        continue;
      }

      if (!($event instanceof TextMessage)) {
        Log::info('Non text event has come');
        continue;
      }

      $this->replyToken=$event->getReplyToken();

      if($event instanceof MessageEvent){

        Log::info('MessageEvent');
        if ($event instanceof TextMessage) {
          Log::info('TextMessage');
          Log::info("userText:".$event->getText());
          $userText = $event->getText();
          //使用者傳什麼，就回答什麼
          $answer = $userText;
          if($answer!=''){
            $resp = $this->replyText($answer);
          }

        }elseif($event instanceof ImageMessage){
          Log::info('ImageMessage');
        }
      }

      if(isset($resp)){
        Log::info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
      }
    }

    return response('OK', 200)
    ->header('Content-Type', 'text/plain');
  }

  //回傳文字
  public function replyText($replyMessage){
        return $this->bot->replyText($this->replyToken, $replyMessage);
  }

}
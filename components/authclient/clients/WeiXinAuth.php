<?php
/**
 * eBestMall
 * ============================================================================
 * Copyright 2015-2017 HongYuKeJi.Co.Ltd. All rights reserved.
 * Http://www.hongyuvip.com
 * ----------------------------------------------------------------------------
 * 仅供学习交流使用，如需商用请购买商用版权。
 * 堂堂正正做人，踏踏实实做事。
 * ----------------------------------------------------------------------------
 * Author: Shadow  QQ: 1527200768  Time: 2017/12/22 13:38
 * E-mail: hongyukeji@126.com
 * ============================================================================
 */

namespace app\components\authclient\clients;

use yii\authclient\OAuth2;
use yii\authclient\InvalidResponseException;
use yii\httpclient\Request;
use yii\httpclient\Response;

class WeiXinAuth extends OAuth2
{
    public $authUrl = 'https://open.weixin.qq.com/connect/qrconnect';
    public $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    public $apiBaseUrl = 'https://api.weixin.qq.com';
    public $scope = 'snsapi_login';

    /**
     * Composes user authorization URL.
     * @param array $params additional auth GET params.
     * @return string authorization URL.
     */
    public function buildAuthUrl(array $params = [])
    {
        $defaultParams = [
            'appid' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->getReturnUrl(),
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }
        if ($this->validateAuthState) {
            $authState = $this->generateAuthState();
            $this->setState('authState', $authState);
            $defaultParams['state'] = $authState;
        }
        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }

    /**
     * Fetches access token from authorization code.
     * @param string $authCode authorization code, usually comes at $_GET['code'].
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @throws HttpException on invalid auth state in case [[enableStateValidation]] is enabled.
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        if ($this->validateAuthState) {
            $authState = $this->getState('authState');
            if (!isset($_REQUEST['state']) || empty($authState) || strcmp($_REQUEST['state'], $authState) !== 0) {
                throw new HttpException(400, 'Invalid auth state parameter.');
            } else {
                $this->removeState('authState');
            }
        }
        $defaultParams = [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'code' => $authCode,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->getReturnUrl(),
        ];
        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));
        $response = $this->sendRequest($request);
        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);
        return $token;
    }

    /**
     * Handles [[Request::EVENT_BEFORE_SEND]] event.
     * Applies [[accessToken]] to the request.
     * @param \yii\httpclient\RequestEvent $event event instance.
     * @throws Exception on invalid access token.
     * @since 2.1
     */
    public function beforeApiRequestSend($event)
    {
        $request = $event->request;
        $data = $request->getData();
        $data['openid'] = $this->getOpenid();
        $request->setData($data);
        parent::beforeApiRequestSend($event);
    }

    /**
     *
     * @return []
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=doc&id=open1419316518&t=0.14920092844688204
     */
    protected function initUserAttributes()
    {
        return $this->api('sns/userinfo');
    }

    /**
     * get UserInfo
     * @return array
     */
    public function getUserInfo()
    {
        return $this->getUserAttributes();
    }

    /**
     * @return string
     */
    public function getOpenid()
    {
        return $this->getAccessToken()->getParam('openid');
    }

    protected function defaultName()
    {
        return 'weixin';
    }

    protected function defaultTitle()
    {
        return 'WeiXin';
    }
}
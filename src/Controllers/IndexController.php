<?php

namespace Larapps\GiftCertificateManager\Controllers;

use Larapps\GiftCertificateManager\Connectors\BigCommerceAgent;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Session;
use Illuminate\Http\Request;


class IndexController {

    private $bcAgent;

    public function __construct(BigCommerceAgent $bcAgent){
        $this->bcAgent = $bcAgent;
    }

    public function index(Request $request): Response
    {
        // Session::put("store_hash", "pmuz4olkpi");
        $this->bcAgent->initStoreDetails( Session::get("store_hash") );

        $requestParams = [
            'keyword' => $request->get("keyword"),
            'limit' => $request->get('limit') ? intval($request->get('limit')) : env('ENTITY_LIMIT'),
            'page' => $request->get('page') ? intval($request->get('page')) : 1,
        ];

        $giftCertificates = $this->bcAgent->getGiftCertificates($requestParams)->object();
        
        return Inertia::render('GiftCertificates/Index', [
            'gift_certificates' => ($giftCertificates === null) ? [] : $giftCertificates
        ]);
    }

    public function load(Request $request): JsonResponse
    {
        $this->bcAgent->initStoreDetails( Session::get("store_hash") );

        $requestParams = [
            'keyword' => $request->get("keyword"),
            'limit' => $request->get('limit') ? intval($request->get('limit')) : env('ENTITY_LIMIT'),
            'page' => $request->get('page') ? intval($request->get('page')) : 1,
        ];

        $data = $this->bcAgent->getGiftCertificates($requestParams)->object();

        return response()->json($data);
    }

}
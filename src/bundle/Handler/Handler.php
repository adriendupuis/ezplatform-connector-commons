<?php

namespace AdrienDupuis\EzPlatformConnectorCommonsBundle\Handler;

use Ibexa\Platform\Contracts\Connector\Dam\Asset;
use Ibexa\Platform\Contracts\Connector\Dam\AssetCollection;
use Ibexa\Platform\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Platform\Contracts\Connector\Dam\AssetMetadata;
use Ibexa\Platform\Contracts\Connector\Dam\AssetSource;
use Ibexa\Platform\Contracts\Connector\Dam\AssetUri;
use Ibexa\Platform\Contracts\Connector\Dam\Handler\Handler as HandlerInterface;
use Ibexa\Platform\Contracts\Connector\Dam\Search\AssetSearchResult;
use Ibexa\Platform\Contracts\Connector\Dam\Search\Query;

class Handler implements HandlerInterface
{
    public function search(Query $query, int $offset = 0, int $limit = 20): AssetSearchResult
    {
        // https://commons.wikimedia.org/w/api.php?action=help&modules=query%2Bsearch
        $searchUrl = 'https://commons.wikimedia.org/w/api.php?action=query&list=search&format=json&srnamespace=6'
            .'&srsearch='.urlencode($query->getPhrase())
            .'&sroffset='.$offset
            .'&srlimit='.$limit
        ;
        $response = json_decode(file_get_contents($searchUrl), true);

        $assets = [];
        foreach ($response['query']['search'] as $result) {
            $identifier = str_replace('File:', '', $result['title']);
            $assets[] = $this->fetchAsset($identifier);
        }

        return new AssetSearchResult((int) $response['query']['searchinfo']['totalhits'], new AssetCollection($assets));
    }

    public function fetchAsset(string $id): Asset
    {
        // https://commons.wikimedia.org/w/api.php?action=help&modules=query%2Bimageinfo
        $metadataUrl = 'https://commons.wikipedia.org/w/api.php?action=query&prop=imageinfo&iiprop=extmetadata&format=json'
            .'&titles=File%3a'.urlencode($id)
        ;
        $response = json_decode(file_get_contents($metadataUrl), true);
        $imageInfo = array_values($response['query']['pages'])[0]['imageinfo'][0]['extmetadata'];

        return new Asset(
            new AssetIdentifier($id),
            new AssetSource('commons'),
            // https://commons.wikimedia.org/wiki/Commons:Reusing_content_outside_Wikimedia/technical#Hotlinking
            new AssetUri('https://commons.wikimedia.org/w/index.php?title=Special:Redirect/file/'.urlencode($id)),
            new AssetMetadata([
                'page_url' => "https://commons.wikimedia.org/wiki/File:$id",
                'author' => array_key_exists('Artist', $imageInfo) ? $imageInfo['Artist']['value'] : null,
                'license' => array_key_exists('LicenseShortName', $imageInfo) ? $imageInfo['LicenseShortName']['value'] : null,
                'license_url' => array_key_exists('LicenseUrl', $imageInfo) ? $imageInfo['LicenseUrl']['value'] : null,
            ])
        );
    }
}

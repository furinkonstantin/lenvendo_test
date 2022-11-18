<?php

    class ParsingVideoResources
    {
        
        protected $urls;
        protected $sourceIframe = [
            'youtube' => 'https://www.youtube.com/embed/',
            'vimeo' => 'https://player.vimeo.com/video/'
        ];
        
        public function __construct($urls)
        {
            $this->urls = $urls;
        }
        
        /*
            //Была попытка сделать через проверку по заголовку по существованию источника, но на vimeo-идентификатор  выдёт код сервера все равно 200 при такой ссылке https://www.youtube.com/embed/225408543, а так бы получилось универсально сделать с добавлением проигрывателя для iframe в переменную $sourceIframe
            public function setCheckSourceForIframe($videoID)
            {
                $res = '';
                foreach($this->sourceIframe as $source)
                {
                    $resultSource = $source.$videoID;
                    $resultSourceHeaders = get_headers($resultSource);
                    $answerServer = substr($resultSourceHeaders[0], 9, 3);
                    var_dump($resultSourceHeaders);
                    if ($answerServer == 200)
                    {
                        $res = $resultSource;
                        break;
                    }
                }
                return $res;
            }
        */
        
        public function setCheckSourceForIframe($host, $videoID)
        {
            $res = '';
            switch($host)
            {
                case 'youtube.com':
                case 'youtu.be':
                    $res = $this->sourceIframe['youtube'].$videoID;
                break;
                case 'vimeo.com':
                    $res = $this->sourceIframe['vimeo'].$videoID;
                break;
            }
            return $res;
        }
        
        public function getResultParseUrl($url)
        {
            $res = [];
            $arInfo = parse_url($url);
            $arInfo['host'] = str_replace('www.', '', $arInfo['host']);
            $videoID = '';
            if ($arInfo['query'])
            {
                $explodeQuery = explode('=', $arInfo['query']);
                $videoID = $explodeQuery[1];
            } else {
                $videoID = mb_substr($arInfo['path'], 1);
            }
            
            $source = $this->setCheckSourceForIframe($arInfo['host'], $videoID);
            $iframe = '<iframe width="640" height="360" src="'. $source .'" frameborder="0" allowfullscreen></iframe>';
            $res = [
                'host_name' => $arInfo['host'],
                'video_id' => $videoID,
                'iframe' => $iframe
            ];
            return $res;
        }
        
        public function getParseUrls()
        {
            $res = [];
            if (is_array($this->urls))
            {
                foreach ($this->urls as $url)
                {
                    $res[] = $this->getResultParseUrl($url);
                }
            } else {
                $url = $this->urls;
                $res[] = $this->getResultParseUrl($url);
            }
            return $res;
        }
        
    }
    
    $parsingVideoResources = new ParsingVideoResources(
        [
            'https://www.youtube.com/watch?v=G1IbRujko-A',
            'https://youtu.be/homqyBxHwis',
            'https://vimeo.com/225408543'
        ]
    );
    $arParseVideoUrls = $parsingVideoResources->getParseUrls();
    foreach($arParseVideoUrls as $arParseVideoUrl) {
        echo $arParseVideoUrl['iframe'];
    }
    
?>
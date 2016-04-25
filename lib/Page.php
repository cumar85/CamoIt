<?php

class Page {
    public function getPages($curPage, $items, $itemOnPage)
    {
        $paginator = array();
        if ($curPage < 1 or $items < 1 or $itemOnPage < 1) {
            return $paginator;
        }
        $shownStartPages = 5;
        $shownEndPages = 4;
        $pages = ceil($items / $itemOnPage);
        $curPage = ($curPage > $pages) ? $pages : $curPage;

        $sqlFrom = ($curPage - 1) * $itemOnPage;
        if ($curPage < $pages) {
            $sqlCount = $itemOnPage;
        } else {
            $sqlCount = $itemOnPage - ($pages * $itemOnPage - $items);
        }

        $pagesStart = array();
        if ($pages > $shownStartPages) {

            if($curPage>=$shownStartPages and
                                $curPage < $pages - $shownEndPages ) {
                for ($i = ($curPage + 2) - $shownStartPages;
                    $i <  ($curPage + 2)  ; $i++){
                    $pagesStart[] = $i;
                }
            } elseif($curPage>=$shownStartPages and $curPage == $pages - $shownEndPages) {
                for ($i = ($curPage + 1) - $shownStartPages;
                    $i <  ($curPage + 1)  ; $i++){
                    $pagesStart[] = $i;
                }
            } else {
                for ($i = 1 ; $i <=  $shownStartPages ; $i++){
                    $pagesStart[] = $i;
                }
            }

        } else{
            for ($i = 1 ; $i <=  $pages ; $i++){
                $pagesStart[] = $i;
            }
        }
        $pagesEnd = array();
        if ($pages > $shownStartPages ) {
            if ( $pages > $shownStartPages + $shownEndPages ) {
                for ($i = $pages-$shownEndPages+1; $i <= $pages; $i++){
                    $pagesEnd[] = $i;
                }
            } else {
                for ($i = $shownStartPages+1; $i <= $pages; $i++){
                    $pagesEnd[] = $i;
                }
            }
        }

        $paginator['pagesStart'] = $pagesStart;
        $paginator['pagesEnd'] = $pagesEnd;
        $paginator['cntPages'] = $pages;
        $paginator['items'] = $items;
        $paginator['curPage'] = $curPage;
        $paginator['sqlFrom'] = $sqlFrom;
        $paginator['sqlCount'] = $sqlCount;
        return $paginator;
    }

    public static function getHtmlPages($pages, $href)
    {
        $html = '';
        if (!$pages) { return $html; }
        foreach ($pages['pagesStart'] as $page) {
            $html .="<a href='$href/page/$page'>";
            $html .= "<span ";
            if($page == $pages['curPage']) {
                $html .= "class='curent'";
            }
            $html .= ">$page</span>";

            if($page != $pages['cntPages']) {
                $html .= "<span>, </span>";
            }
            $html .= "</a>";
        }
        if ($pages['pagesEnd']) {

            if(!($pages['pagesStart'][count($pages['pagesStart']) -1 ] +2 > $pages['pagesEnd'][0] )) {
                $html .= " ..., ";
            }

            foreach ($pages['pagesEnd'] as $page) {
                $html .= "<a href='$href/page/$page'>";
                $html .= "<span ";
                if($page == $pages['curPage']) {
                    $html .= "class='curent'";
                }
                $html .= ">$page</span>";
                if($page != $pages['cntPages']) {
                    $html .= "<span>, </span>";
                }
                $html .="</a>";
            }
        }
        echo $html;
        return true;
    }
}

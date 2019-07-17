<?php
//命名空间的名称与上级目录tools一致
//原因：当前Page.class.php类文件会被自动加载机制引入
//在引入的同时会把"tools"变为文件的上级目录，进而获得该Page类文件
namespace Tools;
class Page {
    private $total; //数据表中总记录数
    private $listRows; //每页显示行数
    private $limit;     //setLimit方法拼接出来的 Limit 0,7
    private $uri;
    private $pageNum; //页数
    private $config = array('header' => "个记录", "prev" => "上一页", "next" => "下一页", "first" => "首 页", "last" => "尾 页");
    private $listNum = 8;
    public $pageArr = array("5","10","20","50","100");  //显示条数

    /*
     * $total 
     * $listRows
     */

    public function __construct($total, $listRows = 10, $pa = "") {
        $this->total = $total;
        $this->listRows = $listRows;
        $this->uri = $this->getUri($pa);
        $this->page = !empty($_GET["page"]) ? $_GET["page"] : 1;
        $this->pageNum = ceil($this->total / $this->listRows);
        $this->limit = $this->setLimit();
    }

    private function setLimit() {
        return "Limit " . ($this->page - 1) * $this->listRows . ", {$this->listRows}";
    }

    private function getUri($pa) {
        $url = $_SERVER["REQUEST_URI"] . (strpos($_SERVER["REQUEST_URI"], '?') ? '' : "?") . $pa;
        $parse = parse_url($url);
        if (isset($parse["query"])) {
            parse_str($parse['query'], $params); 
            unset($params["page"]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        return $url;
    }

    function __get($args) {
        if ($args == "limit")
            return $this->limit;
        else
            return null;
    }

    private function start() {
        if ($this->total == 0)
            return 0;
        else
            return ($this->page - 1) * $this->listRows + 1;
    }

    private function end() {
        return min($this->page * $this->listRows, $this->total);
    }

    private function first() {
        $html = "";
        if ($this->page == 1)
            $html.='';
        else
            $html.="<li><a href='{$this->uri}&page=1'>{$this->config["first"]}</a></li>";

        return $html;
    }

    private function prev() {
        $html = "";
        if ($this->page == 1)
            $html.='';
        else
            $html.="<li><a href='{$this->uri}&page=" . ($this->page - 1) . "'>{$this->config["prev"]}</a></li>";

        return $html;
    }

    private function pageList() {
        $linkPage = "";

        $inum = floor($this->listNum / 2);

        for ($i = $inum; $i >= 1; $i--) {
            $page = $this->page - $i;

            if ($page < 1)
                continue;

            $linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
        }

        $linkPage.="<li  class='active'><a>{$this->page}</a></li>";


        for ($i = 1; $i <= $inum; $i++) {
            $page = $this->page + $i;
            if ($page <= $this->pageNum)
                $linkPage.="<li><a href='{$this->uri}&page={$page}'>{$page}</a></li>";
            else
                break;
        }

        return $linkPage;
    }

    private function next() {
        $html = "";
        if ($this->page == $this->pageNum)
            $html.='';
        else
            $html.="<li><a href='{$this->uri}&page=" . ($this->page + 1) . "'>{$this->config["next"]}</a></li>";

        return $html;
    }

    private function last() {
        $html = "";
        if ($this->page == $this->pageNum)
            $html.='';
        else
            $html.="<li><a href='{$this->uri}&page=" . ($this->pageNum) . "'>{$this->config["last"]}</a></li>";

        return $html;
    }

    private function goPage() {
        return '&nbsp;&nbsp;<input type="text" onkeydown="javascript:if(event.keyCode==13){var page=(this.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.value;location=\'' . $this->uri . '&page=\'+page+\'\'}" value="' . $this->page . '" style="width:25px"><input type="button" value="GO" onclick="javascript:var page=(this.previousSibling.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.previousSibling.value;location=\'' . $this->uri . '&page=\'+page+\'\'">&nbsp;&nbsp;';
    }
    /*9，生成pagesize下拉*/
    private function size(){
        //如果存在pagesize则删除
        $url=$this->getUri($pa="");
        $parse = parse_url($url);
        if (isset($parse["query"])) {
            parse_str($parse['query'], $params);
            unset($params["pagesize"]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        $str="<span class='pagination-select'>";
        $str.='每页显示<select class="select" style="width:50px;" onchange="javascript:location=\''.$url.'&pagesize=\'+this.value"   >';
        foreach ($this->pageArr as $v)
        {
            if($v==$_GET['pagesize'])
                $str.="<option value='{$v}' selected>{$v}</option>";
            else
             $str.="<option value='{$v}'>{$v}</option>";
        }
        $str.="</select>条</span>";
        return $str;
    }
    function flist(){
        return $this->size();
    }

    function fpage($display = array(0, 1, 2, 3, 4, 5, 6, 7, 8)) {
        $html[0] = "&nbsp;&nbsp;共有<b>{$this->total}</b>{$this->config["header"]}&nbsp;&nbsp;";
        $html[1] = "&nbsp;&nbsp;每页显示<b>" . ($this->end() - $this->start() + 1) . "</b>条，本页<b>{$this->start()}-{$this->end()}</b>条&nbsp;&nbsp;";
        $html[2] = "&nbsp;&nbsp;<b>{$this->page}/{$this->pageNum}</b>页&nbsp;&nbsp;";
        $html[3] = $this->first();
        $html[4] = $this->prev();
        $html[5] = $this->pageList();
        $html[6] = $this->next();
        $html[7] = $this->last();
        $html[8] = $this->goPage();
        $fpage = '';
        foreach ($display as $index) {
            $fpage.=$html[$index];
        }
        return $fpage;
    }
    
}

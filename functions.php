<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");

function themeConfig($form) {
	$runtime = new Typecho_Widget_Helper_Form_Element_Radio('runtime',
        array('PHP' => _t('PHP显示'),
            'JS' => _t('JS显示'),
            'NONE' => _t('不显示'),
        ),
        'JS', _t('网站显示运行时间设置'), _t('PHP为显示服务器运行时间,JS为自定义时间'));
    $form->addInput($runtime);

    $style_BG = new Typecho_Widget_Helper_Form_Element_Text('style_BG', NULL, NULL, _t('背景图设置'), _t('填入图片 URL 地址，留空为关闭, 一般为http://www.yourblog.com/image.png,支持 https:// 或 //'));
    $form->addInput($style_BG);

    $shortcut_ico = new Typecho_Widget_Helper_Form_Element_Text('shortcut_ico', NULL, NULL, _t('favicon设置'), _t('填写网站图标地址，留空为关闭, 一般为http://www.yourblog.com/image.png,支持 https:// 或 //'));
    $form->addInput($shortcut_ico);

    $NAME = new Typecho_Widget_Helper_Form_Element_Text('NAME', 'innei', NULL, _t('网页标题设置'), _t('支持5个字符'));
    $form->addInput($NAME);
}

function parseContnet($content){ //解析文章 暂只是添加h3,h4锚点,为 <img> 添加 data-action
    //添加导航树
    $torHTML=post_tor($content);
    if($torHTML!=''){
        $content='<div id="torTree"><h4>导航树</h4>'.$torHTML.'</div>'.$content;
	}

    //添加h3,h4锚点
	$ftitle=array();
	preg_match_all('/<h([3-4])>(.*?)<\/h[3-4]>/', $content, $title);
    $num=count($title[0]);
    
	for ($i=0; $i < $num; $i++) { 
		$f=$title[2][$i];
		$type=$title[1][$i];
		if ($type=='3') {
			$ff='<h3 id="anchor-'.$i.'">'.$f.'</h3>';
		}
		if ($type=='4') {
			$ff='<h4 id="anchor-'.$i.'">'.$f.'</h4>';
		}
		array_push($ftitle, $ff);
	}
	for ($i=0; $i < $num; $i++) { 
		$content=str_replace_limit($title[0][$i],$ftitle[$i],$content);
    }
    
    //<img> 添加 data-action
	$fimg=array();
	preg_match_all('/<img (.*?)>/', $content, $img);
    $num=count($img[0]);
    
	for ($i=0; $i < $num; $i++) { 
		$f=$img[1][$i];
		$ff='<img data-action="zoom" '.$f.'>';

		array_push($fimg, $ff);
	}
	for ($i=0; $i < $num; $i++) { 
		$content=str_replace_limit($img[0][$i],$fimg[$i],$content);
    }

	print_r($content);
}
function str_replace_limit($search,$replace,$subject,$limit=1) {
	if (is_array($search)) {
		foreach ($search as $k=>$v) {
			$search[$k]='`'.preg_quote($search[$k],'`').'`';
		}
	} else {
		$search='`'.preg_quote($search,'`').'`';
	}

	return preg_replace($search,$replace,$subject,$limit);
}
function post_tor($content){
	$tor=array();
	$f='';
	preg_match_all('/<h[3-4]>(.*?)<\/h[3-4]>/', $content, $tor_i);
	$num=count($tor_i[0]);
	for ($i=0; $i < $num; $i++) { 
		$n=$i+1;
		$a='<a href="#anchor-'.$n.'">'.$tor_i[0][$i].'</a>';
		$f=$f.$a;
	}
	$f=str_replace('<h3>','<span class="tori">',$f);
	$f=str_replace('</h3>','</span><br>',$f);
	$f=str_replace('<h4>','<span class="torii">',$f);
	$f=str_replace('</h4>','</span><br>',$f);
	if ($num==0) {
		return '';
	} else {
		return $f;
	}
}
?>

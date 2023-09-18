<?php

namespace TypechoPlugin\AAEditor;
interface Module
{
    /**
     * 编辑器页面插入静态资源
     * @return void
     */
    public static function editorStatic(): void;

    /**
     * 前台插入静态资源
     * @param {Widget_Archive} $archive 页面对象
     * @return mixed
     */
    public static function archiveStatic($archive): void;

    /**
     * 正文内容处理
     *
     * @param {string} $text 处理前的 html
     * @return string 处理后的 html
     */
    public static function parseContent($text, $archive): string;

    /**
     * 摘要内容处理
     *
     * @param {string} $text 处理前的 html
     * @return string 处理后的 html
     */
    public static function parseExcerpt($text, $archive): string;
}

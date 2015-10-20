<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Themesettings Extension
 *
 * @category   Ves
 * @package    Ves_Themesettings
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Themesettings_Helper_Export extends Mage_Core_Helper_Abstract
{
    public function exportStaticBlocks($data){
        $cmsStaticBlocksConfig = array();
        if(isset($data['staticblocks'])){
            $cmsStaticBlocksId = $data['staticblocks'];
            $tables = array("cms/block",
                "cms/block_store"
                );
            if($tables){
                $tmp = array();
                foreach($tables as $table) {
                    $table = trim($table);
                    if(!empty($table)) {
                        $tmp[] = trim($table);
                    }
                }
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $tables = $tmp;
                if(count($tables)>0){
                    foreach ($tables as $table_name) {
                        $table_name = trim($table_name);
                        $table_name = strtolower($table_name);
                        $query = 'SELECT * FROM ' . $resource->getTableName($table_name);
                        $dataDb = $readConnection->fetchAll($query);
                        $cmsStaticBlocksConfig['tables'][$table_name] = $dataDb;
                    }
                }

                if(isset($cmsStaticBlocksConfig['tables']['cms/block'])){
                    foreach ($cmsStaticBlocksConfig['tables']['cms/block'] as $k => $v) {
                        if(!in_array($v['block_id'],$cmsStaticBlocksId)){
                            unset($cmsStaticBlocksConfig['tables']['cms/block'][$k]);
                        }
                    }
                }

                if(isset($cmsStaticBlocksConfig['tables']['cms/block_store'])){
                    foreach ($cmsStaticBlocksConfig['tables']['cms/block_store'] as $k => $v) {
                        if(!in_array($v['block_id'],$cmsStaticBlocksId)){
                            unset($cmsStaticBlocksConfig['tables']['cms/block_store'][$k]);
                        }
                    }
                }
            }
        }
        return $cmsStaticBlocksConfig;
    }

    public function exportModules($data){
        $moduleConfig = array();
        $ves = Mage::helper('themesettings');
        //$vesInfo = $ves->getVenusTheme('default');
        //$modules = array();
        if(isset($vesInfo['export']['modules'])){
            //$modules = $vesInfo['export']['modules'];
        }
        $modules = $this->getModuleTables();
        if(isset($data['modules']) && $exportModules = $data['modules']){
            $storeId = $data['stores'];
            if(isset($data['modules']) && is_array($data['modules'])){
                foreach ($exportModules as $k => $v) {
                    $key = strtolower($v);

                    $configFile = Mage::getConfig()->getModuleDir('etc', $v).DS.'system.xml';
                    if(file_exists($configFile)){
                        $string = simplexml_load_file($configFile);
                        $info = Mage::helper('themesettings')->objToArray($string);
                        $config = array();
                        if(isset($info['sections']) && is_array($info['sections'])){
                            foreach ($info['sections'] as $k1 => $v1) {
                                if($result = Mage::getStoreConfig($k1,$storeId)){
                                    $moduleConfig['system_config'][$k1] = $result;
                                }
                            }
                        }
                    }

                    if(isset($modules[$v]) && count($modules[$v])>0){
                        $module = $modules[$v];
                        // Module Table
                        if(isset($module['tables']) ){
                            $tables = isset($module['tables'])?$module['tables']:array();
                            $tables = explode("\n", $tables);
                            if($tables) {
                                $tmp = array();
                                foreach($tables as $table) {
                                    $table = trim($table);
                                    if(!empty($table)) {
                                        $tmp[] = trim($table);
                                    }
                                }
                                $resource = Mage::getSingleton('core/resource');
                                $readConnection = $resource->getConnection('core_read');
                                $tables = $tmp;
                                if(count($tables)>0){
                                    foreach ($tables as $table_name) {
                                        $table_name = trim($table_name);
                                        $table_name = strtolower($table_name);
                                        $query = 'SELECT * FROM ' . $resource->getTableName($table_name);
                                        $dataDb = $readConnection->fetchAll($query);
                                        $moduleConfig['tables'][$table_name] = $dataDb;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $moduleConfig;
    }

    public function exportCmsPages($data){
        // Export CMS Pages
        $cmsPagesConfig = array();
        if(isset($data['cmspages'])){
            $storeId = $data['stores'];
            $cmsPagesId = $data['cmspages'];
            $tables = array("cms/page",
                "cms/page_store"
                );
            $cmsPagesConfig['system_config']['web']['default']['cms_home_page'] = Mage::getStoreConfig('web/default/cms_home_page',$storeId);
            if($tables){
                $tmp = array();
                foreach($tables as $table) {
                    $table = trim($table);
                    if(!empty($table)) {
                        $tmp[] = trim($table);
                    }
                }
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $tables = $tmp;
                if(count($tables)>0){
                    foreach ($tables as $table_name) {
                        $table_name = trim($table_name);
                        $table_name = strtolower($table_name);
                        $query = 'SELECT * FROM ' . $resource->getTableName($table_name);
                        $dataDb = $readConnection->fetchAll($query);
                        $cmsPagesConfig['tables'][$table_name] = $dataDb;
                    }
                }

                if(isset($cmsPagesConfig['tables']['cms/page'])){
                    foreach ($cmsPagesConfig['tables']['cms/page'] as $k => $v) {
                        if(!in_array($v['page_id'],$cmsPagesId)){
                            unset($cmsPagesConfig['tables']['cms/page'][$k]);
                        }
                    }
                }

                if(isset($cmsPagesConfig['tables']['cms/page_store'])){
                    foreach ($cmsPagesConfig['tables']['cms/page_store'] as $k => $v) {
                        if(!in_array($v['page_id'],$cmsPagesId)){
                            unset($cmsPagesConfig['tables']['cms/page_store'][$k]);
                        }
                    }
                }
            }
        }
        return $cmsPagesConfig;
    }

    public function exportWidgets($data){
        // Export Widgets
        $widgetConfig = array();
        if(isset($data['widgets'])){
            $cmsWidgetsId = $data['widgets'];
            $tables = array("widget/widget",
                "widget/widget_instance",
                "widget/widget_instance_page",
                "widget/widget_instance_page_layout",
                "core_layout_link",
                "core_layout_update"
                );

                // Module Table
            if(isset($tables) ){
                $tmp = array();
                foreach($tables as $table) {
                    $table = trim($table);
                    if(!empty($table)) {
                        $tmp[] = trim($table);
                    }
                }
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $tables = $tmp;
                if(count($tables)>0){
                    foreach ($tables as $table_name) {
                        $table_name = trim($table_name);
                        $table_name = strtolower($table_name);
                        $query = 'SELECT * FROM ' . $resource->getTableName($table_name);
                        $dataDb = $readConnection->fetchAll($query);
                        $widgetConfig['tables'][$table_name] = $dataDb;
                    }
                }

                if(isset($widgetConfig['tables']['widget/widget_instance'])){
                    foreach ($widgetConfig['tables']['widget/widget_instance'] as $k => $v) {
                        if(!in_array($v['instance_id'],$cmsWidgetsId)){
                            unset($widgetConfig['tables']['widget/widget_instance'][$k]);
                        }
                    }
                }

                $page_id = array();
                if(isset($widgetConfig['tables']['widget/widget_instance_page'])){
                    foreach ($widgetConfig['tables']['widget/widget_instance_page'] as $k => $v) {
                        if(!in_array($v['instance_id'],$cmsWidgetsId)){
                            unset($widgetConfig['tables']['widget/widget_instance_page'][$k]);
                        }else{
                            $page_id[] = $v['page_id'];
                        }
                    }
                }

                $layout_update_id = array();
                if(isset($widgetConfig['tables']['widget/widget_instance_page_layout']) && count($page_id)>0){
                    foreach ($widgetConfig['tables']['widget/widget_instance_page_layout'] as $k => $v) {
                        if(!in_array($v['page_id'],$page_id)){
                            unset($widgetConfig['tables']['widget/widget_instance_page_layout'][$k]);
                        }else{
                            $layout_update_id[] = $v['layout_update_id'];
                        }
                    }
                }

                if(isset($widgetConfig['tables']['core_layout_link']) && count($layout_update_id)>0){
                    foreach ($widgetConfig['tables']['core_layout_link'] as $k => $v) {
                        if(!in_array($v['layout_update_id'],$layout_update_id)){
                            unset($widgetConfig['tables']['core_layout_link'][$k]);
                        }
                    }
                }

                if(isset($widgetConfig['tables']['core_layout_update']) && count($layout_update_id)>0 ){
                    foreach ($widgetConfig['tables']['core_layout_update'] as $k => $v) {
                        if(!in_array($v['layout_update_id'],$layout_update_id)){
                            unset($widgetConfig['tables']['core_layout_update'][$k]);
                        }
                    }
                }
            }
        }
        return $widgetConfig;
    }

	/**
    * Write Sample Data to File. Store in folder: "skin/frontend/default/ves theme name/import/"
    */
    public function writeSampleDataFile($importDir, $file_name, $content = "") {
        
            $filePath = $importDir. DS . $file_name;
            $file = new Varien_Io_File(); 
            $file->setAllowCreateFolders(true);
            $file->open(array( 'path' => $importDir ));
            $file->streamOpen($filePath, 'w+', 0777);
            $file->streamLock(true);
            $file->streamWrite($content);
            $file->streamUnlock();
            $file->streamClose();
       
    }

    public function getModuleTables($module_name = "") {
        $sql_tables = array("Ves_Tempcp" => array("theme", "theme_store", "module"),
            "Ves_Blog" => array("category", "category_store", "comment", "comment_store", "post", "post_store"),
            "Ves_Brand" => array("brand", "brand_store"),
            "Ves_Verticalmenu" => array("verticalmenu", "verticalmenu_store", "verticalmenu_widget"),
            "Ves_Layerslider" => array("banner", "banner_store"),
            "Ves_Contentslider" => array("banner", "banner_store"),
            "Ves_Megamenu" => array("megamenu", "megamenu_store", "megamenu_widget"),
            "Ves_BlockBuilder" => array("block", "template"),
            "Ves_Landingpage" => array("slider", "slider_store"),
            "Ves_Testimonial" => array("testimonial", "testimonial_store"),
            "Ves_Artist" => array("group", "group_store", "artist", "artist_store"),
            "Ves_FAQ" => array("category", "category_store", "question", "question_store", "answer", "answer_store"),
            "Ves_Parallax" => array("banner", "banner_store"),
            "Ves_Map" => array("group", "group_store", "location", "location_store"),
            "Ves_Gallery" => array("banner"));
return $sql_tables;
}

}
<?php

namespace Apps\ContentBuilder;

use Sellfino\DB;
use Sellfino\Helpers;
use Sellfino\Shopify;

Class App
{

  public function install()
  {

    Shopify::createAssetsTheme();

  }

  public function uninstall() {}

  public function router($route)
  {

    switch ($route) {

      case 'fieldsets':
        $this->fieldsets();
        break;

      case 'fieldset':
        $this->fieldset();
        break;

      case 'update':
        $this->update();
        break;
      
      case 'toggle':
        $this->toggle();
        break;
      
      case 'delete':
        $this->delete();
        break;
      
      case 'content':
        $this->content();
        break;
      
      case 'save':
        $this->save();
        break;
      
      case 'upload':
        $this->upload();
        break;

    }
    
  }

  public function fieldsets()
  {

    $items = DB::get('fieldsets', 'ContentBuilder.json');
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

    usort($items, function($a, $b) {
      return strtotime($b["name"]) - strtotime($a["name"]);
    });

    $cut = array_slice($items, $page * 50 - 50, $page * 50);

    Helpers::json($cut);

  }

  public function fieldset()
  {

    $items = DB::get('fieldsets', 'ContentBuilder.json');
    $found = array_search($_REQUEST['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      Helpers::json($items[$found]);

    } else {

      Helpers::error(404);

    }

  }

  public function update()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('fieldsets', 'ContentBuilder.json');

    foreach ($items as $item) {
      if ($item['name'] == $data['name']) {
        if (isset($data['id'])) {
          if ($item['id'] != $data['id']) {
            Helpers::error(400, 'This name is already taken');
          }
        } else {
          Helpers::error(400, 'This name is already taken');
        }
      }
    }

    if (isset($data['id'])) {

      $found = array_search($data['id'], array_column($items, 'id'));

      if (!is_bool($found)) {

        $items[$found] = $data;
        DB::put('fieldsets', $items, 'ContentBuilder.json');

      }

    } else {

      $data['id'] = uniqid();
      $items[] = $data;
      DB::put('fieldsets', $items, 'ContentBuilder.json');

      Helpers::json([
        'id' => $data['id']
      ]);

    }

    Helpers::success();

  }

  public function toggle()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('fieldsets', 'ContentBuilder.json');
    $found = array_search($data['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      $items[$found]['active'] = !$items[$found]['active'];
      DB::put('fieldsets', $items, 'ContentBuilder.json');
      
    }

    Helpers::success();

  }

  public function delete()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('fieldsets', 'ContentBuilder.json');
    $found = array_search($data['id'], array_column($items, 'id'));

    if (!is_bool($found)) {

      unset($items[$found]);
      DB::put('fieldsets', $items, 'ContentBuilder.json');
      
    }

    Helpers::success();

  }

  private function metaToObj($data, $full = false)
  {

    $content = Shopify::request($data['type'] . '/' . $data['id'] . '/metafields');
    $content = json_decode($content, true);

    $metafields = array_map(function($arr) {
      return array_intersect_key($arr, array_flip(['id','key','value']));
    }, $content['metafields']);

    $content = [];

    if ($full) {

      foreach ($metafields as $key => $metafield) {
        $content[$metafield['key']] = $metafield;
      }

    } else {

      foreach ($metafields as $key => $metafield) {
        $json = json_decode($metafield['value']);
        $content[$metafield['key']] = $json && $metafield['value'] != $json ? json_decode($metafield['value'], true) : $metafield['value'];
      }

    }

    return $content;

  }

  public function content()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $items = DB::get('fieldsets', 'ContentBuilder.json');

    Helpers::json([
      'fieldsets' => array_filter($items, function($fieldset) { return $fieldset['active']; }),
      'content' => $this->metaToObj($data)
    ]);

  }

  public function save()
  {

    $data = json_decode(file_get_contents('php://input'), true);
    $old_content = $this->metaToObj($data, true);
    $new_content = $data['content'];
    $update = [];
    $delete = [];

    foreach ($new_content as $key => $field) {
      $add = false;

      if (!isset($old_content[$key])) {
        if (is_array($field)) {
          if (count($field)) {
            $add = true;
          }
        } else if ($field != '') {
          $add = true;
        }
      } else {
        if ($old_content[$key] != $field) {
          $add = true;
        }
      }

      if ($add) {
        $update[] = [
          'namespace' => 'scb',
          'key' => $key,
          'value' => is_array($field) ? json_encode($field) : $field,
          'value_type' => is_array($field) ? 'json_string' : 'string'
        ];
      }
    }

    foreach ($old_content as $key => $meta) {
      if (!isset($new_content[$key])) {
        $delete[] = $meta['id'];
      } else {
        if (is_array($new_content[$key]) && count($new_content[$key]) == 0) {
          $delete[] = $meta['id'];
        } else {
          if ($new_content[$key] == '') {
            $delete[] = $meta['id'];
          }
        }
      }
    }

    $path = $data['type'] . '/' . $data['id'];

    if ($data['blog']) {
      $path = 'blogs/' . $data['blog'] . '/' . $path;
    }

    foreach ($update as $meta) {
      Shopify::request($path . '/metafields', ['metafield' => $meta] , 'POST', true);
    }

    foreach ($delete as $id) {
      Shopify::request($path . '/metafields/' . $id, [] , 'DELETE');
    }

    Helpers::success();

  }

  public function upload()
  {

    if (isset($_FILES['files'])) {

      $assets = [];
      $themeId = DB::get('assets_theme');
      $len = count($_FILES['files']['name']);

      for($i = 0; $i < $len; $i++) {

        $data = file_get_contents($_FILES['files']['tmp_name'][$i]);
        $base64 = base64_encode($data);
        
        $params = [
          'asset' => [
            'key' => 'assets/' . uniqid() . '_' . $_FILES['files']['name'][$i],
            'attachment' => $base64
          ],
          'nopag' => true
        ];

        $asset_json = json_decode(Shopify::request('themes/' . $themeId . '/assets', $params, 'PUT'), true);

        if (!isset($asset_json['errors'])) {

          $assets[] = $asset_json['asset']['public_url'];

        }

      }

      $theme_assets = DB::get('assets', 'assets.json');
      $theme_assets = array_merge($theme_assets, $assets);
      DB::put('assets', $theme_assets, 'assets.json');

      Helpers::json(array_map(function($n) {
        return [
          'url' => $n,
          'title' => ''
        ];
      }, $assets));

    }

  }
  
}
<?php
if (!defined('ABSPATH')) exit;

class EMJ_Justified_Widget extends \Elementor\Widget_Base {
  public function get_name(){ return 'emj_justified'; }
  public function get_title(){ return 'Masonry Justified (Vanilla)'; }
  public function get_icon(){ return 'eicon-gallery-justified'; }
  public function get_categories(){ return ['general']; }
  public function get_keywords(){ return ['gallery','justified','masonry','grid','images','lightbox','carousel','slider']; }
  public function get_script_depends() { return [ 'emj-justified' ]; }
  public function get_style_depends() { return [ 'emj-justified' ]; }

  protected function register_controls(){
    $this->start_controls_section('section_content', ['label'=>__('Contenido','emj')]);

    $this->add_control('images', [
      'label' => __('Imágenes','emj'),
      'type'  => \Elementor\Controls_Manager::GALLERY,
      'default' => []
    ]);

    // Orden
    $this->add_control('orderby', [
      'label' => __('Orden','emj'),
      'type'  => \Elementor\Controls_Manager::SELECT,
      'options' => [
        'post__in'   => __('Como seleccionadas (arrastre en la galería)','emj'),
        'date'       => __('Fecha','emj'),
        'title'      => __('Título','emj'),
        'menu_order' => __('Orden del adjunto','emj'),
        'rand'       => __('Aleatorio','emj'),
      ],
      'default' => 'post__in'
    ]);
    $this->add_control('order', [
      'label' => __('Dirección','emj'),
      'type'  => \Elementor\Controls_Manager::SELECT,
      'options' => ['ASC'=>'ASC','DESC'=>'DESC'],
      'default' => 'ASC',
      'condition' => ['orderby!' => 'rand']
    ]);

    // Tipo de Layout
    $this->add_control('layout_type', [
      'label'   => __('Tipo de Layout','emj'),
      'type'    => \Elementor\Controls_Manager::SELECT,
      'options' => [
        'grid'     => __('Grilla (Justified/Mosaic)','emj'),
        'carousel' => __('Carrusel (Swiper)','emj'),
      ],
      'default' => 'grid'
    ]);

    // Modo de layout (solo para grilla)
    $this->add_control('mode', [
      'label' => __('Modo de grilla','emj'),
      'type'  => \Elementor\Controls_Manager::SELECT,
      'options' => [
        'justified' => __('Justified (sin huecos)','emj'),
        'mosaic'    => __('Mosaico (rowspan, bloques grandes)','emj'),
      ],
      'default'   => 'mosaic',
      'condition' => ['layout_type' => 'grid']
    ]);

    $this->end_controls_section();

    // SECTION CAROUSEL
    $this->start_controls_section('section_carousel', [
      'label'     => __('Ajustes de Carrusel','emj'),
      'condition' => ['layout_type' => 'carousel']
    ]);

    $this->add_responsive_control('slides_per_view', [
      'label'   => __('Diapositivas por vista','emj'),
      'type'    => \Elementor\Controls_Manager::NUMBER,
      'min'     => 1, 'max' => 10, 'step' => 0.1, 'default' => 3,
      'tablet_default' => 2,
      'mobile_default' => 1,
    ]);

    $this->add_control('autoplay', [
      'label'   => __('Autoplay','emj'),
      'type'    => \Elementor\Controls_Manager::SWITCHER,
      'default' => 'yes'
    ]);

    $this->add_control('autoplay_speed', [
      'label'     => __('Velocidad Autoplay (ms)','emj'),
      'type'      => \Elementor\Controls_Manager::NUMBER,
      'default'   => 3000,
      'condition' => ['autoplay' => 'yes']
    ]);

    $this->add_control('loop', [
      'label'   => __('Bucle infinito','emj'),
      'type'    => \Elementor\Controls_Manager::SWITCHER,
      'default' => 'yes'
    ]);

    $this->add_control('arrows', [
      'label'   => __('Mostrar Flechas','emj'),
      'type'    => \Elementor\Controls_Manager::SWITCHER,
      'default' => 'yes'
    ]);

    $this->add_control('pagination', [
      'label'   => __('Mostrar Paginación','emj'),
      'type'    => \Elementor\Controls_Manager::SWITCHER,
      'default' => 'yes'
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_grid_settings', [
      'label' => __('Ajustes de Grilla','emj'),
      'condition' => ['layout_type' => 'grid']
    ]);

    // Parámetros globales de grilla
    $this->add_control('gap', [
      'label' => __('Gap (px)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>0, 'max'=>60, 'step'=>1, 'default'=>6
    ]);

    // JUSTIFIED
    $this->add_control('target_row_height', [
      'label' => __('Altura objetivo de fila (px)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>120, 'max'=>800, 'step'=>10, 'default'=>360,
      'condition' => ['mode' => 'justified']
    ]);
    $this->add_control('last_row', [
      'label' => __('Última fila','emj'),
      'type'  => \Elementor\Controls_Manager::SELECT,
      'options' => [
        'center'  => __('Centrar (sin estirar)','emj'),
        'justify' => __('Justificar (rellenar el ancho)','emj'),
        'hide'    => __('Ocultar','emj'),
      ],
      'default' => 'center',
      'condition' => ['mode' => 'justified']
    ]);
    $this->add_control('row_height_tablet', [
      'label' => __('Altura fila Tablet (factor x)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>0.4, 'max'=>2, 'step'=>0.05, 'default'=>0.85,
      'condition' => ['mode' => 'justified']
    ]);
    $this->add_control('row_height_mobile', [
      'label' => __('Altura fila Móvil (factor x)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>0.3, 'max'=>2, 'step'=>0.05, 'default'=>0.7,
      'condition' => ['mode' => 'justified']
    ]);

    // MOSAICO (rowspan)
    $this->add_control('span_every', [
      'label' => __('Cada cuántas verticales hacer “grande”','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>1,'max'=>12,'step'=>1,'default'=>3,
      'condition' => ['mode' => 'mosaic']
    ]);
    $this->add_control('span_factor', [
      'label' => __('Altura del bloque grande (x fila)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>1.2,'max'=>3,'step'=>0.1,'default'=>2,
      'condition' => ['mode' => 'mosaic']
    ]);
    $this->add_control('span_target_row', [
      'label' => __('Altura base de fila (px)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>120,'max'=>800,'step'=>10,'default'=>320,
      'condition' => ['mode' => 'mosaic']
    ]);
    $this->add_control('span_min_side', [
      'label' => __('Mín. fotos al lado del bloque (por fila)','emj'),
      'type'  => \Elementor\Controls_Manager::NUMBER,
      'min'=>1,'max'=>6,'step'=>1,'default'=>2,
      'condition' => ['mode' => 'mosaic']
    ]);

    $this->end_controls_section();

    // Miniaturas y captions
    $this->start_controls_section('section_style', ['label'=>__('Estilo','emj')]);

    $this->add_control('thumb_size', [
      'label' => __('Tamaño de miniatura','emj'),
      'type'  => \Elementor\Controls_Manager::TEXT,
      'placeholder' => 'large, full, 2048x2048...',
      'default' => 'large'
    ]);
    $this->add_control('show_captions', [
      'label' => __('Mostrar títulos/leyendas','emj'),
      'type'  => \Elementor\Controls_Manager::SWITCHER,
      'default' => ''
    ]);

    $this->end_controls_section();
  }

  protected function render(){
    wp_enqueue_style ('emj-justified');
    wp_enqueue_script('emj-justified');

    $s = $this->get_settings_for_display();

    // IDs
    $ids = [];
    if (!empty($s['images']) && is_array($s['images'])){
      foreach ($s['images'] as $img) {
        if (!empty($img['id'])) $ids[] = (int)$img['id'];
      }
    }

    if (!$ids){
      if (\Elementor\Plugin::$instance->editor->is_edit_mode()){
        echo '<div style="padding:12px;border:1px dashed #ccc;">'.esc_html__('Selecciona imágenes para mostrar la galería.','emj').'</div>';
      }
      return;
    }

    // Orden
    $order   = (!empty($s['order']) && in_array($s['order'],['ASC','DESC'],true)) ? $s['order'] : 'ASC';
    $orderby = !empty($s['orderby']) ? $s['orderby'] : 'post__in';

    $items = [];
    foreach ($ids as $id){
      $p = get_post($id);
      if (!$p) continue;
      $meta  = wp_get_attachment_metadata($id);
      $w     = isset($meta['width'])  ? (int)$meta['width']  : 0;
      $h     = isset($meta['height']) ? (int)$meta['height'] : 0;
      $thumb = wp_get_attachment_image_src($id, $s['thumb_size'] ?: 'large');
      $full  = wp_get_attachment_image_src($id, 'full');
      if (!$thumb || !$full) continue;

      $alt     = get_post_meta($id, '_wp_attachment_image_alt', true);
      $alt     = $alt ? $alt : get_the_title($id);
      $caption = trim($p->post_excerpt);

      $items[] = [
        'id'=>$id,'w'=>$w,'h'=>$h,'thumb'=>$thumb[0],'full'=>$full[0],
        'alt'=>$alt,'cap'=>$caption,'portrait' => ($h>$w && $w>0)
      ];
    }

    if ($orderby === 'rand') {
      shuffle($items);
    } elseif ($orderby === 'post__in') {
      $map = array_flip($ids);
      usort($items, function($a, $b) use ($map) {
        return $map[$a['id']] <=> $map[$b['id']];
      });
    } elseif ($orderby === 'date') {
      usort($items, function($a, $b) {
        $pa = get_post($a['id']); $pb = get_post($b['id']);
        return strtotime($pa->post_date) <=> strtotime($pb->post_date);
      });
    } elseif ($orderby === 'title') {
      usort($items, function($a, $b) {
        return strcasecmp(get_the_title($a['id']), get_the_title($b['id']));
      });
    } elseif ($orderby === 'menu_order') {
      usort($items, function($a, $b) {
        $pa = get_post($a['id']); $pb = get_post($b['id']);
        return (int)$pa->menu_order <=> (int)$pb->menu_order;
      });
    }
    if ($orderby !== 'rand' && $order === 'DESC') $items = array_reverse($items);

    $uid  = 'emj-'.wp_generate_password(6,false,false);
    $layout = $s['layout_type'] ?? 'grid';

    $cfg  = [
      'layout' => $layout,
      'mode'   => (string)($s['mode'] ?? 'mosaic'),
      'gap'    => (int)($s['gap'] ?? 6),
      // justified
      'target' => (int)($s['target_row_height'] ?? 360),
      'lastRow'=> (string)($s['last_row'] ?? 'center'),
      'factorTablet' => (float)($s['row_height_tablet'] ?? 0.85),
      'factorMobile' => (float)($s['row_height_mobile'] ?? 0.7),
      // mosaic
      'spanEvery'  => (int)($s['span_every'] ?? 3),
      'spanFactor' => (float)($s['span_factor'] ?? 2),
      'spanTarget' => (int)($s['span_target_row'] ?? 320),
      'spanMinSide'=> (int)($s['span_min_side'] ?? 2),
      // carousel
      'carousel' => [
        'slidesPerView' => $s['slides_per_view'] ?? 3,
        'slidesPerViewTablet' => $s['slides_per_view_tablet'] ?? 2,
        'slidesPerViewMobile' => $s['slides_per_view_mobile'] ?? 1,
        'autoplay' => ($s['autoplay'] === 'yes') ? (int)$s['autoplay_speed'] : false,
        'loop'     => ($s['loop'] === 'yes'),
        'arrows'   => ($s['arrows'] === 'yes'),
        'pagination' => ($s['pagination'] === 'yes'),
      ],
      'captions'   => !empty($s['show_captions']),
    ];

    echo '<div class="emj-scope">';

    if ($layout === 'carousel') {
        echo '<div class="swiper emj-carousel" id="'.esc_attr($uid).'" data-emj="'.esc_attr(wp_json_encode($cfg)).'">';
        echo '<div class="swiper-wrapper">';
        foreach ($items as $it) {
            echo '<div class="swiper-slide">';
            $this->render_item($it, $cfg, $uid);
            echo '</div>';
        }
        echo '</div>';
        if ($cfg['carousel']['pagination']) echo '<div class="swiper-pagination"></div>';
        if ($cfg['carousel']['arrows']) {
            echo '<div class="swiper-button-prev"></div>';
            echo '<div class="swiper-button-next"></div>';
        }
        echo '</div>';
    } else {
        echo '<div class="emj-wrap" id="'.esc_attr($uid).'" data-emj="'.esc_attr(wp_json_encode($cfg)).'">';
        foreach ($items as $it) {
            $this->render_item($it, $cfg, $uid);
        }
        echo '</div>';
    }

    echo '</div>'; // .emj-scope
  }

  protected function render_item($it, $cfg, $uid) {
      $ratio = ($it['h']>0) ? ($it['w']/$it['h']) : 1.5;
      
      // GLightbox integration
      $glightbox_data = 'gallery: ' . esc_attr($uid);
      if (!empty($cfg['captions'])) {
          $glightbox_data .= '; title: ' . esc_attr($it['alt']) . '; description: ' . esc_attr($it['cap']);
      }

      echo '<a class="emj-item glightbox"'
         . ' data-elementor-open-lightbox="no"'
         . ' href="'.esc_url($it['full']).'"'
         . ' data-ratio="'.esc_attr($ratio).'"'
         . ' data-glightbox="'. $glightbox_data . '"'
         . ' data-portrait="'.($it['portrait']?'1':'0').'"'
         . '>';

      echo '<img src="'.esc_url($it['thumb']).'" alt="'.esc_attr($it['alt']).'" loading="lazy" />';

      if (!empty($cfg['captions']) && $it['cap']){
        echo '<span class="emj-cap">'.esc_html($it['cap']).'</span>';
      }
      echo '</a>';
  }
}

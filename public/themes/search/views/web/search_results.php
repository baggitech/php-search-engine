<?php
defined('FIR') OR exit();
/**
 * Template para exibição da página de Resultados de Busca Web
 * 
 * Este arquivo define a estrutura de exibição dos resultados de busca web, incluindo:
 * - Anúncios (topo e rodapé)
 * - Correção de consulta (se aplicável)
 * - Resultados web principais
 * - Resultados mistos (imagens, vídeos, notícias)
 * - Buscas relacionadas
 * - Paginação
 * 
 * Funcionalidades:
 * - Exibe anúncios se habilitados e safe search permitir
 * - Mostra sugestões de correção de consulta
 * - Renderiza diferentes tipos de resultados baseado na resposta da API
 * - Links para mais resultados de cada tipo
 * - Buscas relacionadas para expandir a consulta
 */
?>
<div class="row">
    <!-- Anúncio do topo (se habilitado e permitido) -->
    <?=($data['show_ads'] ? $data['settings']['ads_2'] : false)?>
    
    <div class="web-results">
        <!-- Correção de consulta (se a API sugerir) -->
        <?php if(isset($data['response']['queryContext']['alteredQuery'])): ?>
            <div class="query-spell">
                <div class="spell-title">
                    <?=sprintf($lang['showing_results_for'], '<a href="'.$data['url'].'/'.e($this->url[0]).'?q='.urlencode($data['response']['queryContext']['alteredQuery']).'">'.e($data['response']['queryContext']['alteredQuery']).'</a>')?>
                </div>
                <div class="spell-description">
                    <?=sprintf($lang['search_instead_for'], '<a href="'.$data['url'].'/'.e($this->url[0]).'?q='.urlencode($data['response']['queryContext']['alterationOverrideQuery']).'">'.e($data['response']['queryContext']['originalQuery']).'</a>')?>
                </div>
            </div>
        <?php endif ?>

        <!-- Loop através dos itens de resposta da API -->
        <?php foreach($data['response']['rankingResponse']['mainline']['items'] as $item): ?>
            <!-- Resultados de páginas web -->
            <?php if($item['answerType'] == "WebPages"): ?>
                <?=$this->render(['results' => $data['response']['webPages']['value'][$item['resultIndex']]], 'web/rows')?>
            <?php endif ?>

            <!-- Resultados de imagens (máximo 3) -->
            <?php if($item['answerType'] == "Images" && isset($data['response']['images']['value'])): ?>
                <div class="response-title response-images"><?=$lang['images']?></div>
                <?=$this->render(['results' => array_slice($data['response']['images']['value'], 0, 3)], 'images/rows')?>
                <?php if($data['settings']['images_per_page'] > 0): ?>
                    <div class="response-footer response-images"><a href="<?=$data['url']?>/images?q=<?=e($data['query'])?>"><?=sprintf($lang['more_x'], $lang['images'])?></a></div>
                <?php endif ?>
            <?php endif ?>

            <!-- Resultados de vídeos (máximo 3) -->
            <?php if($item['answerType'] == "Videos" && isset($data['response']['videos']['value'])): ?>
                <div class="response-title response-videos"><?=$lang['videos']?></div>
                <?=$this->render(['results' => array_slice($data['response']['videos']['value'], 0, 3)], 'videos/rows')?>
                <?php if($data['settings']['videos_per_page'] > 0): ?>
                    <div class="response-footer response-videos"><a href="<?=$data['url']?>/videos?q=<?=e($data['query'])?>"><?=sprintf($lang['more_x'], $lang['videos'])?></a></div>
                <?php endif ?>
            <?php endif ?>

            <!-- Resultados de notícias (máximo 3) -->
            <?php if($item['answerType'] == "News" && isset($data['response']['news']['value'])): ?>
                <div class="response-title response-news"><?=$lang['news']?></div>
                <?=$this->render(['results' => array_slice($data['response']['news']['value'], 0, 3)], 'news/rows')?>
                <?php if($data['settings']['news_per_page'] > 0): ?>
                    <div class="response-footer response-news"><a href="<?=$data['url']?>/news?q=<?=e($data['query'])?>"><?=sprintf($lang['more_x'], $lang['news'])?></a></div>
                <?php endif ?>
            <?php endif ?>
        <?php endforeach ?>

        <!-- Buscas relacionadas (se disponíveis) -->
        <?php if(isset($data['response']['relatedSearches'])): ?>
            <?=$this->render(['results' => $data['response']['relatedSearches']['value'], 'query' => $data['query']], 'web/related_searches')?>
        <?php endif ?>
    </div>
    
    <!-- Anúncio do rodapé (se habilitado e permitido) -->
    <?=($data['show_ads'] ? $data['settings']['ads_3'] : false)?>
</div>

<!-- Paginação dos resultados -->
<div class="row">
    <?=$data['pagination_view']?>
</div>
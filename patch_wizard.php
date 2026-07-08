<?php
$content = file_get_contents('/var/www/pendaftaran/index.php');

// 1. Add Wizard Header
$wizard_header = <<<HTML
        <div>
          <h1 class="text-gelombang" id="text-gelombang"></h1>
           <p class="text-semester">
            Semester Genap 2025- 2026 dan Semester Gasal 2026-2027
           </p>
        </div>

        <!-- Wizard Progress -->
        <div class="wizard-progress">
          <div class="step-indicator active" data-step="0">1</div>
          <div class="step-line"></div>
          <div class="step-indicator" data-step="1">2</div>
          <div class="step-line"></div>
          <div class="step-indicator" data-step="2">3</div>
          <div class="step-line"></div>
          <div class="step-indicator" data-step="3">4</div>
          <div class="step-line"></div>
          <div class="step-indicator" data-step="4">5</div>
          <div class="step-line"></div>
          <div class="step-indicator" data-step="5">6</div>
          <div class="step-line"></div>
          <div class="step-indicator" data-step="6">7</div>
        </div>
HTML;

$content = preg_replace('/<div>\s*<h1 class="text-gelombang" id="text-gelombang"><\/h1>.*?<\/p>\s*<\/div>/s', $wizard_header, $content);

// 2. Wrap jenjang-options in a container for step 0
$content = preg_replace('/<div class="form-radio">\s*<!-- Pilih Jenjang -->\s*<label>Pilih Jenjang <span class="required">\*<\/span><\/label>\s*<div id="jenjang-options" class="radio-group">\s*<\/div>\s*<\/div>/s', '<div id="step-0" class="wizard-step active">
          <div class="form-radio">
            <label>Pilih Jenjang <span class="required">*</span></label>
            <div id="jenjang-options" class="radio-group"></div>
          </div>
          <div class="wizard-nav">
             <!-- <button type="button" class="btn-next" onclick="nextStep(1)">Selanjutnya</button> -->
          </div>
        </div>', $content);

// 3. For each container, wrap it and add nav buttons
$steps = [
    'prodi-container' => 1,
    'lokasi-kampus-container' => 2,
    'jenis-pendaftaran-container' => 3,
    'waktu-perkuliahan-container' => 4,
    'jalur-masuk-container' => 5
];

foreach ($steps as $id => $step_index) {
    $prev_step = $step_index - 1;
    $next_step = $step_index + 1;
    
    // We add a wrapper to each existing container or replace the existing one with a wizard-step class
    // Since the existing containers use id="$id" and class="hidden form-radio", let's modify them.
    $content = preg_replace('/<div id="' . $id . '" class="hidden form-radio">/', '<div id="step-' . $step_index . '" class="wizard-step hidden"> <div id="' . $id . '" class="form-radio">', $content);
    
    // The closing tag for each container needs to include the nav buttons.
    // It's a bit tricky with regex. Let's do it manually for each block.
}
file_put_contents('/var/www/pendaftaran/index.php', $content);
?>

<?php

namespace Drupal\user_profile_account\Form;
#namespace Drupal\photography_qa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PhotographyQAForm extends FormBase {

  public function getFormId() {
    return 'photography_qa_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['question'] = [
      '#type' => 'textfield',
      '#title' => t('Ask a Photography Question'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Ask'),
    ];

    if ($answer = $form_state->get('answer')) {
      $form['response'] = [
        '#markup' => '<p><strong>Answer:</strong> ' . $answer . '</p>',
      ];
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $question = strtolower($form_state->getValue('question'));
    $answer = $this->getAnswer($question);
    $form_state->set('answer', $answer);
  }

  private function getAnswer($question) {
    $rules = [
      'aperture' => 'Aperture controls the amount of light entering the camera. A lower f-stop (e.g., f/2.8) means a wider aperture and more background blur.',
      'shutter speed' => 'Shutter speed determines how long the sensor is exposed to light. A fast speed (1/1000s) freezes motion, while a slow speed (1/30s) creates motion blur.',
      'iso' => 'ISO controls the sensor’s sensitivity to light. A higher ISO (e.g., 3200) is useful in low light but may introduce noise.',
      'rule of thirds' => 'The Rule of Thirds is a composition guideline where the frame is divided into a 3x3 grid, and subjects are placed along the lines or intersections.',
      'white balance' => 'White balance adjusts color temperature to ensure accurate colors. Common presets include daylight, cloudy, and tungsten.',
      'depth of field' => 'Depth of field refers to the area in focus in a photo. A shallow depth (f/2.8) isolates the subject, while a deep depth (f/11) keeps more in focus.',
      'exposure triangle' => 'The Exposure Triangle consists of ISO, Aperture, and Shutter Speed. Adjusting one setting affects the others to balance exposure.',
      'golden hour' => 'Golden Hour is the period shortly after sunrise and before sunset, offering soft, warm light ideal for photography.',
      'blue hour' => 'Blue Hour is the time just before sunrise and after sunset, giving a cool, atmospheric tone to photos.',
      'bokeh' => 'Bokeh is the aesthetic quality of blurred areas in an image, usually achieved with a wide aperture (f/1.8, f/2.8).',
      'leading lines' => 'Leading Lines are compositional elements that guide the viewer’s eye through an image, such as roads, rivers, or fences.',
      'long exposure' => 'Long Exposure uses a slow shutter speed (e.g., 10s) to capture motion effects like light trails or smooth water.',
      'hdr' => 'HDR (High Dynamic Range) combines multiple exposures to retain details in both shadows and highlights.',
      'macro photography' => 'Macro Photography captures extreme close-ups of small subjects, often using a macro lens with a 1:1 magnification ratio.',
      'low light photography' => 'For low light photography, use a fast lens (f/1.8), increase ISO, and stabilize the camera with a tripod.',
      'night photography' => 'For night photography, use a slow shutter speed, low aperture (f/2.8), and adjust ISO while keeping noise minimal.',
      'street photography' => 'Street photography captures candid moments in public places. Use a fast shutter speed and be observant of unique scenes.',
      'portrait photography' => 'For portraits, use a wide aperture (f/2.8) to blur the background and focus on the subject. Good lighting enhances the shot.',
      'wildlife photography' => 'Wildlife photography requires a telephoto lens (200mm+), fast shutter speed, and patience to capture animals in motion.',
      'landscape photography' => 'Use a narrow aperture (f/11-f/16) and a tripod for sharp landscapes. Golden hour provides ideal lighting.',
      'action photography' => 'For action shots, use a fast shutter speed (1/1000s or higher) and continuous autofocus to freeze motion.',
      'black and white photography' => 'Black and white photography focuses on contrast and texture. Shoot in RAW and convert in post-processing.',
      'composition' => 'Good composition includes using rules like the rule of thirds, leading lines, symmetry, and framing to create engaging images.',
      'camera sensor' => 'A camera sensor determines image quality. Full-frame sensors offer better low-light performance, while crop sensors provide extra reach.',
      'film photography' => 'Film photography requires selecting the right film type (color or black & white) and understanding film speed (ISO).',
      'dslr vs mirrorless' => 'DSLR cameras have optical viewfinders and longer battery life, while mirrorless cameras are lighter and offer electronic viewfinders.',

    ];
    
    foreach ($rules as $keyword => $response) {
      if (strpos($question, $keyword) !== FALSE) {
        return $response;
      }
    }
    
    return "I/'m not sure about that. Try asking about camera settings, lighting, or composition!";
  }
}
  

<?php
/**
 * Classe QuestionAudio
 * Permet de jouer un extrait musical avant de répondre
 *
 * 📚 STRUCTURE IDENTIQUE À QuestionImage
 * La seule différence est l'élément HTML utilisé :
 * - QuestionImage utilise <img>
 * - QuestionAudio utilise <audio>
 */

class QuestionAudio extends Question {
    private string $mediaUrl;

    public function __construct(
        int $id,
        string $texteQuestion,
        array $reponses,
        int $bonneReponse,
        string $mediaUrl,
        ?string $explication = null
    ) {
        parent::__construct($id, $texteQuestion, $reponses, $bonneReponse, $explication);
        $this->mediaUrl = $mediaUrl;
    }

    public function getMediaUrl(): string {
        return $this->mediaUrl;
    }

    public function afficherHTML(int $index): string {
        $html = '';

        $html .= '<div class="bg-white rounded-2xl shadow-xl p-6 mb-6">';

        // En-tête
        $html .= '<div class="flex items-start gap-4 mb-6">';
        $html .= '<span class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm flex-shrink-0">';
        $html .= ($index + 1);
        $html .= '</span>';
        $html .= '<h3 class="text-xl font-semibold text-gray-800 leading-relaxed">';
        $html .= htmlspecialchars($this->texteQuestion);
        $html .= '</h3>';
        $html .= '</div>';

        // ====== NOUVEAU : LECTEUR AUDIO HTML5 ======
        // 📚 CONCEPT : Balise <audio> HTML5
        // controls = affiche les boutons play/pause/volume
        // L'utilisateur peut écouter l'extrait autant de fois qu'il veut
        $html .= '<div class="mb-6 flex justify-center">';
        $html .= '<audio controls class="w-full max-w-md">';
        $html .= '<source src="' . htmlspecialchars($this->mediaUrl) . '" type="audio/mpeg">';
        $html .= 'Votre navigateur ne supporte pas l\'élément audio.';
        $html .= '</audio>';
        $html .= '</div>';
        // ============================================

        // Réponses
        $html .= '<div class="space-y-3">';
        foreach ($this->reponses as $idx => $reponse) {
            $html .= '<label class="flex items-center p-4 rounded-xl border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 cursor-pointer transition-all duration-200 group">';
            $html .= '<input type="radio" name="reponses[' . $index . ']" value="' . $idx . '" class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500 focus:ring-2" required>';
            $html .= '<span class="ml-3 text-gray-700 group-hover:text-purple-700 font-medium">';
            $html .= htmlspecialchars($reponse);
            $html .= '</span>';
            $html .= '</label>';
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function getType(): string {
        return 'audio';
    }
}

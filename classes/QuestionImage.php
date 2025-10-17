<?php
/**
 * Classe QuestionImage
 *
 * 📚 CONCEPT POO : SPÉCIALISATION PAR HÉRITAGE
 * QuestionImage hérite de Question mais AJOUTE une propriété supplémentaire
 *
 * DIFFÉRENCE AVEC QuestionTexte :
 * - QuestionTexte n'a que les propriétés de base
 * - QuestionImage ajoute $mediaUrl pour stocker le chemin de l'image
 */

class QuestionImage extends Question {
    // 📚 CONCEPT : Propriété supplémentaire spécifique à ce type
    // Cette propriété n'existe PAS dans Question ni dans QuestionTexte
    private string $mediaUrl;

    /**
     * Constructeur avec paramètre supplémentaire
     *
     * 📚 CONCEPT : APPEL DU CONSTRUCTEUR PARENT
     * On utilise parent::__construct() pour appeler le constructeur de Question
     * Puis on initialise notre propriété supplémentaire $mediaUrl
     *
     * POURQUOI ?
     * Pour éviter de réécrire toute la logique d'initialisation
     * On réutilise le code du parent
     */
    public function __construct(
        int $id,
        string $texteQuestion,
        array $reponses,
        int $bonneReponse,
        string $mediaUrl,           // 📚 NOUVEAU PARAMÈTRE
        ?string $explication = null
    ) {
        // 📚 CONCEPT : parent::
        // Appelle la méthode du même nom dans la classe parente
        // Ici, on appelle le constructeur de Question
        parent::__construct($id, $texteQuestion, $reponses, $bonneReponse, $explication);

        // On initialise notre propriété spécifique
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * Getter pour accéder à l'URL du média
     */
    public function getMediaUrl(): string {
        return $this->mediaUrl;
    }

    /**
     * Génère le HTML avec l'affichage de l'image
     *
     * 📚 CONCEPT : REDÉFINITION DE MÉTHODE (override)
     * On implémente afficherHTML() différemment de QuestionTexte
     * L'affichage inclut maintenant une image
     */
    public function afficherHTML(int $index): string {
        $html = '';

        // Card container
        $html .= '<div class="bg-white rounded-2xl shadow-xl p-6 mb-6">';

        // En-tête (identique à QuestionTexte)
        $html .= '<div class="flex items-start gap-4 mb-6">';
        $html .= '<span class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm flex-shrink-0">';
        $html .= ($index + 1);
        $html .= '</span>';
        $html .= '<h3 class="text-xl font-semibold text-gray-800 leading-relaxed">';
        $html .= htmlspecialchars($this->texteQuestion);
        $html .= '</h3>';
        $html .= '</div>';

        // ====== NOUVEAU : AFFICHAGE DE L'IMAGE ======
        // 📚 CONCEPT : Ajout d'un élément visuel spécifique
        $html .= '<div class="mb-6 flex justify-center">';
        $html .= '<img ';
        $html .= 'src="' . htmlspecialchars($this->mediaUrl) . '" ';
        $html .= 'alt="Question visuelle" ';
        $html .= 'class="max-w-md w-full rounded-xl shadow-lg object-cover"';  // Tailwind pour le responsive
        $html .= '>';
        $html .= '</div>';
        // ============================================

        // Réponses (identique à QuestionTexte)
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
        return 'image';
    }
}

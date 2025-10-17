<?php
/**
 * Classe QuestionTexte
 *
 * 📚 CONCEPT POO : HÉRITAGE
 * Le mot-clé "extends" signifie que QuestionTexte HÉRITE de Question
 *
 * CONSÉQUENCES DE L'HÉRITAGE :
 * ✅ QuestionTexte possède TOUTES les propriétés de Question (id, texteQuestion, etc.)
 * ✅ QuestionTexte possède TOUTES les méthodes de Question (getId(), estCorrect(), etc.)
 * ✅ QuestionTexte DOIT implémenter les méthodes abstraites (afficherHTML(), getType())
 * ✅ QuestionTexte peut AJOUTER ses propres propriétés et méthodes
 *
 * ANALOGIE :
 * Question = "Véhicule" (abstrait)
 * QuestionTexte = "Voiture" (concret, type de véhicule)
 */

class QuestionTexte extends Question {

    /**
     * Génère le HTML pour afficher une question texte
     *
     * 📚 CONCEPT : IMPLÉMENTATION D'UNE MÉTHODE ABSTRAITE
     * Cette méthode était abstraite dans Question
     * On DOIT la définir ici avec du code réel
     *
     * @param int $index Position de la question (1, 2, 3...)
     * @return string Code HTML complet de la question
     */
    public function afficherHTML(int $index): string {
        // 📚 CONCEPT : Concaténation de chaînes avec .=
        // On construit le HTML morceau par morceau
        $html = '';

        // === CARD CONTAINER ===
        // 📚 Classes Tailwind pour le design
        $html .= '<div class="bg-white rounded-2xl shadow-xl p-6 mb-6">';

        // === EN-TÊTE DE LA QUESTION ===
        $html .= '<div class="flex items-start gap-4 mb-6">';

        // 📚 Badge numéroté avec gradient violet
        $html .= '<span class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm flex-shrink-0">';
        $html .= ($index + 1);  // +1 car l'index commence à 0 mais on veut afficher 1, 2, 3...
        $html .= '</span>';

        // 📚 CONCEPT : Protection XSS avec htmlspecialchars()
        // TOUJOURS échapper les données qui viennent de l'extérieur !
        // Convertit < en &lt;, > en &gt;, etc.
        // Empêche l'injection de code JavaScript malveillant
        $html .= '<h3 class="text-xl font-semibold text-gray-800 leading-relaxed">';
        $html .= htmlspecialchars($this->texteQuestion);
        $html .= '</h3>';

        $html .= '</div>'; // Fin de l'en-tête

        // === LISTE DES RÉPONSES ===
        $html .= '<div class="space-y-3 ml-12">';

        // 📚 CONCEPT : Boucle foreach pour parcourir le tableau des réponses
        // $idx = index (0, 1, 2, 3)
        // $reponse = texte de la réponse
        foreach ($this->reponses as $idx => $reponse) {
            // 📚 Label cliquable contenant le bouton radio + texte
            // Cliquer n'importe où sur le label sélectionne le radio button
            $html .= '<label class="flex items-center p-4 rounded-xl border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 cursor-pointer transition-all duration-200 group">';

            // 📚 CONCEPT : Input radio avec name identique pour le groupement
            // name="reponses[0]" pour la question 0
            // name="reponses[1]" pour la question 1, etc.
            // Tous les radios d'une même question ont le même name
            // value = l'index de cette réponse (0, 1, 2 ou 3)
            $html .= '<input type="radio" ';
            $html .= 'name="reponses[' . $index . ']" ';
            $html .= 'value="' . $idx . '" ';
            $html .= 'class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500 focus:ring-2" ';
            $html .= 'required>';  // required = obligatoire

            // Texte de la réponse (protégé contre XSS)
            $html .= '<span class="ml-3 text-gray-700 group-hover:text-purple-700 font-medium">';
            $html .= htmlspecialchars($reponse);
            $html .= '</span>';

            $html .= '</label>';
        }

        $html .= '</div>'; // Fin de la liste des réponses
        $html .= '</div>'; // Fin de la card

        return $html;
    }

    /**
     * Retourne le type de cette question
     *
     * 📚 CONCEPT : Implémentation simple d'une méthode abstraite
     * Chaque classe fille retourne son type spécifique
     *
     * @return string 'texte'
     */
    public function getType(): string {
        return 'texte';
    }
}

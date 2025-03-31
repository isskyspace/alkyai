require("dotenv").config();
const express = require("express");
const nodemailer = require("nodemailer");
const stripe = require("stripe")(process.env.STRIPE_SECRET_KEY); // Utilise la clé secrète Stripe
const cors = require("cors");

const app = express();

// Middleware
app.use(express.json());
app.use(cors()); // Active CORS pour accepter les requêtes depuis n'importe où

// Configurer le transporteur pour les e-mails
const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: process.env.SMTP_PORT,
    secure: true, // true pour le port 465, false pour les autres
    auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
    }
});

// Route pour envoyer un email via le formulaire de contact
app.post("/send-email", async (req, res) => {
    const { name, email, message } = req.body;

    try {
        await transporter.sendMail({
            from: `"${name}" <${email}>`,
            to: "admin@alkyai.fr",
            subject: "Nouveau message via le formulaire de contact",
            text: message
        });
        res.status(200).json({ success: true, message: "Votre Email a été envoyé !" });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// Route pour créer la session de paiement avec un montant sélectionné
app.post('/create-checkout-session', async (req, res) => {
    try {
        const { amount } = req.body;

        // Validation du montant
        if (isNaN(amount) || amount <= 0) {
            throw new Error("Le montant doit être un nombre valide.");
        }

        // Créez une session de paiement Stripe avec un montant dynamique
        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: [{
                price_data: {
                    currency: 'eur', // Devise : euros
                    product_data: {
                        name: 'Don pour AlkyBin', // Nom du produit
                        description: 'Soutenez le projet AlkyBin', // Description du produit
                    },
                    unit_amount: Math.round(amount * 100), // Montant en centimes
                },
                quantity: 1, // Quantité
            }],
            mode: 'payment', // Mode de paiement
            success_url: 'https://alkyai.fr/success.html', // URL de succès
            cancel_url: 'https://alkyai.fr/cancel.html',  // URL d'annulation
        });

        // Renvoyez l'ID de la session au client
        res.json({ id: session.id });
    } catch (error) {
        console.error("Erreur lors de la création de la session Stripe :", error); // Log pour les erreurs
        res.status(500).json({ error: error.message });
    }
});

// Route pour créer la session de paiement pour la formation (90 € fixe)
app.post('/create-checkout-session-formation', async (req, res) => {
    try {
        // Créez une session de paiement Stripe avec un montant fixe
        const session = await stripe.checkout.sessions.create({
            payment_method_types: ['card'],
            line_items: [{
                price_data: {
                    currency: 'eur', // Devise : euros
                    product_data: {
                        name: 'Formation IA/Business/OFM', // Nom du produit
                        description: 'Formation complète : Format PDF', // Description du produit
                    },
                    unit_amount: 3000, // Montant en centimes (90 € = 9000 centimes)
                },
                quantity: 1, // Quantité
            }],
            mode: 'payment', // Mode de paiement
            success_url: 'https://alkyai.fr/success-formation.html', // URL de succès
            cancel_url: 'https://alkyai.fr/cancel-formation.html',  // URL d'annulation
        });

        // Renvoyez l'ID de la session au client
        res.json({ id: session.id });
    } catch (error) {
        console.error("Erreur lors de la création de la session Stripe :", error); // Log pour les erreurs
        res.status(500).json({ error: error.message });
    }
});

// Démarre le serveur
const PORT = process.env.PORT || 10000; // Utilise le port spécifié dans l'environnement ou 10000
app.listen(PORT, () => console.log(`✅ Serveur en ligne sur le port ${PORT}`));
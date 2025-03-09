require("dotenv").config();
const express = require("express");
const nodemailer = require("nodemailer");
const cors = require("cors");

const app = express();
app.use(express.json());
app.use(cors());

const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: process.env.SMTP_PORT,
    secure: true, // true pour le port 465, false pour les autres
    auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
    }
});

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


// 📌 Lancer le serveur
const PORT = process.env.PORT || 465;

app.listen(PORT, '0.0.0.0', () => console.log(`✅ Serveur en ligne sur le port ${PORT}`));
import express from "express";
import Stripe from "stripe";
import cors from "cors";

const app = express();
const stripe = new Stripe("sk_test_51SDmj71htB3038T8nlt0J9narnVIVmH3sCuFZiIGsWmtA602kFd1iAq5fUCUsnpDtICr0quWuBitOT2XkolSvyP000zuvq47sH"); // chave secreta

app.use(cors());
app.use(express.json());

app.post("/create-payment-intent", async (req, res) => {
  const { amount } = req.body;
  const paymentIntent = await stripe.paymentIntents.create({
    amount: amount, // em centavos
    currency: "brl",
    automatic_payment_methods: { enabled: true },
  });
  res.json({ clientSecret: paymentIntent.client_secret });
});

app.listen(3000, () => console.log("Servidor rodando na porta 3000"));

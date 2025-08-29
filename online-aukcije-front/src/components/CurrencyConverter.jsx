import React, { useState } from "react";
import axios from "axios";

const CurrencyConverter = ({ amountInRSD }) => {
  const [convertedAmount, setConvertedAmount] = useState(null);
  const [targetCurrency, setTargetCurrency] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleConvert = async (currency) => {
    setIsLoading(true);
    setError(null);
    setConvertedAmount(null);
    setTargetCurrency(currency);

    const apiKey = "7a95ddcd5afb19218eef9d4d";
    const apiUrl = `https://v6.exchangerate-api.com/v6/${apiKey}/latest/RSD`;

    try {
      const response = await axios.get(apiUrl);

      if (response.data && response.data.result === "success") {
        const rate = response.data.conversion_rates[currency];

        const result = amountInRSD * rate;

        setConvertedAmount(result.toFixed(2));
      } else {
        setError("Nije moguce dobiti kursnu listu.");
      }
    } catch (err) {
      console.error("Greska pri pozivu API-ja za konverziju:", err);
      setError("Doslo je do greske. Pokusajte ponovo.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="currency-converter">
      <div className="conversion-buttons">
        <button onClick={() => handleConvert("EUR")} disabled={isLoading}>
          Pretvori u EUR
        </button>
        <button onClick={() => handleConvert("USD")} disabled={isLoading}>
          Pretvori u USD
        </button>
      </div>

      {isLoading && <p>Konvertovanje...</p>}

      {error && <p className="error-message">{error}</p>}

      {convertedAmount && (
        <p className="converted-amount">
          To je otprilike:{" "}
          <strong>
            {convertedAmount} {targetCurrency}
          </strong>
        </p>
      )}
    </div>
  );
};

export default CurrencyConverter;

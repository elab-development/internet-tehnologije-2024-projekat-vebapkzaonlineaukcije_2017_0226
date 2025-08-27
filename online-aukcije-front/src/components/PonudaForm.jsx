import React, { useState } from "react";
import axios from "axios";

const PonudaForm = ({ aukcijaId, onBidSuccess, aukcija }) => {
  const [iznos, setIznos] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    setSuccess(null);

    const trenutnaCenaZaProracun =
      aukcija.trenutna_cena || aukcija.pocetna_cena;
    const minimalniIznos = aukcija.trenutna_cena
      ? trenutnaCenaZaProracun + 100
      : aukcija.pocetna_cena;

    if (parseFloat(iznos) < minimalniIznos) {
      setError(`Ponuda mora biti najmanje ${minimalniIznos} RSD.`);
      setLoading(false);
      return;
    }

    try {
      const token = localStorage.getItem("authToken");

      if (!token) {
        setError("Niste ulogovani.");
        setLoading(false);
        return;
      }
      const response = await axios.post(
        `http://localhost:8000/api/aukcije/${aukcijaId}/ponudi`,
        { iznos: iznos },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      console.log("Odgovor sa servera:", response.data);

      const updatedAukcija = response?.data?.data?.aukcija;

      if (updatedAukcija) {
        setSuccess(response.data.message);
        setIznos("");
        if (onBidSuccess) {
          onBidSuccess(updatedAukcija);
        }
      } else {
        // Ako 'updatedAukcija' nije pronađen, ispisujemo grešku.
        // Ovo znači da struktura odgovora nije ispravna.
        console.error(
          "Struktura odgovora sa servera je neispravna. Nedostaje 'data.aukcija'."
        );
        setError("Došlo je do greške pri obradi odgovora sa servera.");
      }
    } catch (err) {
      if (err.response && err.response.data && err.response.data.message) {
        setError(err.response.data.message);
      } else {
        setError("Došlo je do greške prilikom postavljanja ponude.");
      }
    } finally {
      setLoading(false);
    }
  };

  const minimalnaPonudaPlaceholder = aukcija.trenutna_cena
    ? aukcija.trenutna_cena + 100
    : aukcija.pocetna_cena;

  return (
    <form onSubmit={handleSubmit} className="ponuda-forma">
      <h3>Postavi ponudu</h3>
      <input
        type="number"
        value={iznos}
        onChange={(e) => setIznos(e.target.value)}
        placeholder={`Minimalna ponuda: ${minimalnaPonudaPlaceholder}`}
        required
      />
      <button type="submit" disabled={loading}>
        {loading ? "Slanje..." : "Ponudi"}
      </button>

      {success && <p className="success-message">{success}</p>}
      {error && <p className="error-message">{error}</p>}
    </form>
  );
};

export default PonudaForm;

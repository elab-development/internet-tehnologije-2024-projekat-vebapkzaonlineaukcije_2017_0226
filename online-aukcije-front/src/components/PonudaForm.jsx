import React, { useState } from "react";
import axios from "axios";

const PonudaForm = ({ aukcijaId, onBidSuccess, trenutnaCena }) => {
  const [iznos, setIznos] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    setSuccess(null);

    const minimalniIznos = (trenutnaCena || 0) + 1;
    if (parseFloat(iznos) <= minimalniIznos) {
      setError(`Iznos mora biti veći od trenutne cene.`);
      setLoading(false);
      return;
    }

    try {
      const response = await axios.post(
        `http://localhost:8000/api/ponuda/${aukcijaId}`,
        { iznos: iznos }
      );
      setSuccess(response.data.message);
      setIznos("");

      onBidSuccess();
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

  return (
    <form onSubmit={handleSubmit} className="ponuda-forma">
      <h3>Postavi ponudu</h3>
      <input
        type="number"
        value={iznos}
        onChange={(e) => setIznos(e.target.value)}
        placeholder="Unesite iznos"
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

import React, { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";
import PonudaForm from "./PonudaForm";
import CountdownTimer from "./CountdownTimer";

const AukcijaDetailsPage = () => {
  const { id } = useParams();

  const [aukcija, setAukcija] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchAukcijaDetails = async () => {
    try {
      const response = await axios.get(
        `http://localhost:8000/api/aukcije/${id}`
      );
      setAukcija(response.data.data);
    } catch (err) {
      setError("Došlo je do greške prilikom učitavanja aukcije.");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchAukcijaDetails();

    const interval = setInterval(fetchAukcijaDetails, 5000);
    return () => clearInterval(interval);
  }, [id]);

  if (isLoading) {
    return <div>Učitavanje detalja aukcije...</div>;
  }

  if (error) {
    return <div>Greška: {error}</div>;
  }

  if (!aukcija) {
    return <div>Aukcija nije pronadjena.</div>;
  }

  const trenutnaCena =
    aukcija.ponude && aukcija.ponude.length > 0
      ? aukcija.ponude[0].iznos
      : aukcija.pocetna_cena;

  return (
    <div className="auction-details-page">
      <h2>{aukcija.naziv}</h2>
      <p>
        <strong>Status:</strong> {aukcija.status_aukcije}
      </p>
      <p>
        <strong>Početna cena:</strong> {aukcija.pocetna_cena} RSD
      </p>
      <p>
        <strong>Trenutna cena:</strong> {trenutnaCena} RSD
      </p>

      <div className="timer-area">
        {aukcija.status_aukcije === "predstojeca" && (
          <>
            <p>Aukcija počinje za:</p>
            <CountdownTimer targetDate={aukcija.datum_pocetka} />
          </>
        )}
        {aukcija.status_aukcije === "aktivna" && (
          <>
            <p>Aukcija se završava za:</p>
            <CountdownTimer targetDate={aukcija.vreme_isteka} />
          </>
        )}
        {aukcija.status_aukcije === "zavrsena" && <p>Aukcija je završena.</p>}
      </div>

      {aukcija.status_aukcije === "aktivna" && (
        <PonudaForm
          aukcijaId={aukcija.id}
          onBidSuccess={fetchAukcijaDetails}
          trenutnaCena={trenutnaCena}
        />
      )}

      <h3>Detalji proizvoda:</h3>
      {aukcija.proizvodi.map((proizvod, index) => (
        <div key={index}>
          <h4>{proizvod.naziv}</h4>
          <p>
            <strong>Opis:</strong> {proizvod.opis}
          </p>
          <p>
            <strong>Kategorija:</strong> {proizvod.kategorija}
          </p>
          <p>
            <strong>Stanje:</strong> {proizvod.stanje}
          </p>
          {proizvod.slika_url && (
            <img
              src={`http://localhost:8000${proizvod.slika_url}`}
              alt={proizvod.naziv}
              style={{ width: "300px" }}
            />
          )}
        </div>
      ))}
    </div>
  );
};

export default AukcijaDetailsPage;

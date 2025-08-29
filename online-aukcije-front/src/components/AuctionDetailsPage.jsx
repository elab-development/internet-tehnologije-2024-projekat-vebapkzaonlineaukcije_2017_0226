import React, { useState, useEffect, useCallback, useContext } from "react";
import { useParams, useNavigate } from "react-router-dom";
import axios from "axios";
import PonudaForm from "./PonudaForm";
import CountdownTimer from "./CountdownTimer";
import { AuthContext } from "../context/AuthContext";

const AuctionDetailsPage = () => {
  const { id } = useParams();
  const [aukcija, setAukcija] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  const navigate = useNavigate();
  const { isLoggedIn, loadingAuth, logout, isAdmin, userId } =
    useContext(AuthContext);

  const fetchAukcijaDetails = useCallback(async () => {
    if (loadingAuth) {
      return;
    }

    try {
      const authToken = localStorage.getItem("authToken");
      const headers = {};

      if (authToken) {
        headers.Authorization = `Bearer ${authToken}`;
      }

      const response = await axios.get(
        `http://localhost:8000/api/aukcije/${id}`,
        { headers }
      );

      setAukcija(response.data.data);
      console.log(
        "fetchAukcijaDetails: Aukcija dohvaćena.",
        response.data.data
      );

      if (response.data.data.moja_najvisa_ponuda_iznos) {
        console.log(
          "fetchAukcijaDetails: moja_najvisa_ponuda_iznos je prisutan:",
          response.data.data.moja_najvisa_ponuda_iznos
        );
      } else {
        console.log(
          "fetchAukcijaDetails: moja_najvisa_ponuda_iznos NIJE prisutan."
        );
      }
    } catch (err) {
      if (
        err.response &&
        (err.response.status === 401 || err.response.status === 403)
      ) {
        console.error(
          "fetchAukcijaDetails: Autentifikacija neuspešna. Odjavljivanje..."
        );
        await logout();
      }
      setError("Došlo je do greške prilikom učitavanja aukcije.");
      console.error("fetchAukcijaDetails: Greška prilikom dohvatanja:", err);
    } finally {
      setIsLoading(false);
    }
  }, [id, logout, loadingAuth]);

  useEffect(() => {
    if (loadingAuth) return;

    console.log("useEffect: Pokrećem inicijalno dohvatanje i polling.");
    fetchAukcijaDetails();

    const intervalId = setInterval(() => {
      console.log("Polling: Pozivam fetchAukcijaDetails...");
      fetchAukcijaDetails();
    }, 2000);

    return () => {
      console.log("useEffect cleanup: Zaustavljam polling.");
      clearInterval(intervalId);
    };
  }, [fetchAukcijaDetails, loadingAuth]);

  const handleBidSuccess = useCallback((azuriranaAukcija) => {
    console.log(
      "handleBidSuccess: Primljen je azuriran objekat aukcije.",
      azuriranaAukcija
    );
    setAukcija(azuriranaAukcija);

    console.log("handleBidSuccess: Stanje aukcije je trenutno azurirano.");
  }, []);

  if (loadingAuth || isLoading) {
    return <div>Učitavanje detalja aukcije...</div>;
  }

  if (error) {
    return <div>Greška: {error}</div>;
  }

  if (!aukcija) {
    return <div>Aukcija nije pronadjena.</div>;
  }

  const trenutnaCena = aukcija.trenutna_cena
    ? aukcija.trenutna_cena
    : "Nema ponuda";

  let statusPonudeKorisnika = null;
  if (isLoggedIn && aukcija.status_aukcije === "aktivna") {
    if (
      aukcija.moja_najvisa_ponuda_iznos === aukcija.trenutna_cena &&
      aukcija.moja_najvisa_ponuda_iznos !== null
    ) {
      statusPonudeKorisnika = (
        <p className="bid-status-leading">
          <strong>Vodiš u aukciji!</strong>
        </p>
      );
    } else if (aukcija.moja_najvisa_ponuda_iznos !== null) {
      statusPonudeKorisnika = (
        <p className="bid-status-losing">
          <strong>Trenutno gubiš u aukciji!.</strong>
        </p>
      );
    }
  }

  const handleDelete = async () => {
    if (
      window.confirm("Da li ste sigurni da želite da obrišete ovu aukciju?")
    ) {
      try {
        const token = localStorage.getItem("authToken");
        await axios.delete(`http://localhost:8000/api/aukcije/${aukcija.id}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        navigate("/");
      } catch (error) {
        console.error("Greška pri brisanju aukcije:", error);
      }
    }
  };

  console.log("Ulogovan si:", isLoggedIn);
  console.log("Da li si admin:", isAdmin);
  console.log("Tvoj ID:", userId);
  console.log("ID vlasnika aukcije:", aukcija.korisnik_id);
  console.log("Da li se ID-evi poklapaju:", userId === aukcija.korisnik_id);

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
        <strong>Trenutna cena:</strong> {trenutnaCena}{" "}
        {trenutnaCena !== "Nema ponuda" && "RSD"}
      </p>

      <div className="delete-auctions-admin">
        {isLoggedIn && isAdmin && (
          <button className="delete1-btn" onClick={handleDelete}>
            Obriši Aukciju
          </button>
        )}
      </div>

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
            <CountdownTimer
              key={aukcija.vreme_isteka}
              targetDate={aukcija.vreme_isteka}
            />
          </>
        )}
        {aukcija.status_aukcije === "zavrsena" && <p>Aukcija je završena.</p>}
      </div>

      {statusPonudeKorisnika}

      {aukcija.status_aukcije === "aktivna" && (
        <PonudaForm
          aukcijaId={aukcija.id}
          onBidSuccess={handleBidSuccess}
          aukcija={aukcija}
        />
      )}

      <h3>Proizvodi:</h3>
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

export default AuctionDetailsPage;

import React, { useState, useEffect } from "react";
import axios from "axios";
import AuctionCard from "./AuctionCard";

const HomePage = () => {
  const [auctions, setAuctions] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  const [status, setStatus] = useState("");
  const [sortBy, setSortBy] = useState("");

  useEffect(() => {
    const fetchAuctions = async () => {
      try {
        setIsLoading(true);
        const params = {};
        if (status) {
          params.status_aukcije = status;
        }
        if (sortBy) {
          params.sort_by = sortBy;
        }

        const response = await axios.get("http://localhost:8000/api/aukcije", {
          params,
        });
        setAuctions(response.data.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setIsLoading(false);
      }
    };

    fetchAuctions();
  }, [status, sortBy]);

  if (isLoading) {
    return <div>Učitavanje aukcija...</div>;
  }

  if (error) {
    return <div>Došlo je do greške: {error}</div>;
  }

  if (auctions.length === 0) {
    return <div className="no-auctions-state">Nema aukcija za prikaz.</div>;
  }

  return (
    <div>
      <div className="home-page-container">
        <h1>Sve Aukcije</h1>

        <div className="filters-container">
          <label htmlFor="status-filter">Filtriraj po statusu:</label>
          <select
            id="status-filter"
            value={status}
            onChange={(e) => setStatus(e.target.value)}
          >
            <option value="">Svi statusi</option>
            <option value="predstojeca">Predstojeca</option>
            <option value="aktivna">Aktivna</option>
            <option value="zavrsena">Zavrsena</option>
          </select>

          <label htmlFor="sort-by">Sortiraj po pocetnoj ceni:</label>
          <select
            id="sort-by"
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
          >
            <option value="">Bez sortiranja</option>
            <option value="cena_asc">Sortiraj rastuce</option>
            <option value="cena_desc">Sortiraj opadajuce</option>
          </select>
        </div>

        <div className="auctions-grid">
          {auctions.map((auction) => (
            <AuctionCard key={auction.id} auction={auction} />
          ))}
        </div>
      </div>
    </div>
  );
};

export default HomePage;

import React, { useState, useEffect, useContext } from "react";
import axios from "axios";
import AuctionCard from "./AuctionCard";
import { AuthContext } from "../context/AuthContext";

const HomePage = () => {
  const { auctionUpdateTrigger } = useContext(AuthContext);
  const [auctions, setAuctions] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  const [status, setStatus] = useState("");
  const [sortBy, setSortBy] = useState("");
  const [categorySearchTerm, setCategorySearchTerm] = useState("");

  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(null);

  useEffect(() => {
    setCurrentPage(1);
  }, [status, sortBy, categorySearchTerm]);

  useEffect(() => {
    const fetchAuctions = async () => {
      try {
        setIsLoading(true);
        const apiUrl = "http://localhost:8000/api/aukcije";
        const params = {
          page: currentPage,
        };

        if (status) {
          params.status_aukcije = status;
        }
        if (sortBy) {
          params.sort_by = sortBy;
        }
        if (categorySearchTerm) {
          params.kategorija = categorySearchTerm;
        }

        const response = await axios.get(apiUrl, {
          params,
        });

        setAuctions(response.data.data);
        setLastPage(response.data.meta.last_page);
      } catch (err) {
        setError(
          err.message || "Doslo je do greske prilikom ucitavanja aukcija."
        );
      } finally {
        setIsLoading(false);
      }
    };

    fetchAuctions();
  }, [status, sortBy, currentPage, categorySearchTerm, auctionUpdateTrigger]);

  if (isLoading) {
    return <div>Učitavanje aukcija...</div>;
  }

  if (error) {
    return <div>Došlo je do greške: {error}</div>;
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
            onChange={(e) => {
              setStatus(e.target.value);
            }}
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
            onChange={(e) => {
              setSortBy(e.target.value);
            }}
          >
            <option value="">Bez sortiranja</option>
            <option value="cena_asc">Sortiraj rastuce</option>
            <option value="cena_desc">Sortiraj opadajuce</option>
          </select>

          <label htmlFor="category-search">
            Prikazi aukcije sa zeljenom kategorijom proizvoda:
          </label>
          <select
            id="category-search"
            value={categorySearchTerm}
            onChange={(e) => {
              setCategorySearchTerm(e.target.value);
            }}
          >
            <option value="">Bez pretrage kategorije</option>
            <option value="elektronika">Elektronika</option>
            <option value="odeca">Odeca</option>
            <option value="umetnost">Umetnost</option>
            <option value="sport">Sport</option>
            <option value="kucni aparati">Kucni aparati</option>
            <option value="namestaj">Namestaj</option>
            <option value="vozila">Vozila</option>
            <option value="alati">Alati</option>
            <option value="kucni ljubimci">Kucni ljubimci</option>
            <option value="ostalo">Ostalo</option>
          </select>
        </div>

        <div className="auctions-grid">
          {auctions.length > 0 &&
            auctions.map((auction) => (
              <AuctionCard key={auction.id} auction={auction} />
            ))}
        </div>

        <div className="pagination">
          <button
            onClick={() => setCurrentPage(currentPage - 1)}
            disabled={currentPage === 1}
          >
            Prethodna
          </button>

          <span>
            Stranica {currentPage} od {lastPage}
          </span>

          <button
            onClick={() => setCurrentPage(currentPage + 1)}
            disabled={currentPage === lastPage}
          >
            Sledeća
          </button>
        </div>
      </div>
    </div>
  );
};

export default HomePage;

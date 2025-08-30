import React, { useState, useEffect, useContext } from "react";
import axios from "axios";
import { AuthContext } from "../context/AuthContext";

const MyAuctions = () => {
  const { isLoggedIn, userId, loadingAuth, triggerAuctionUpdate } =
    useContext(AuthContext);
  const [myAuctions, setMyAuctions] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  const [showCreateModal, setShowCreateModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedAuction, setSelectedAuction] = useState(null);

  const [auctionFormData, setAuctionFormData] = useState({
    naziv: "",
    pocetna_cena: "",
    datum_pocetka: "", // YYYY-MM-DD
    vreme_pocetka: "", // HH:MM
  });

  const [productFormsData, setProductFormsData] = useState([
    { naziv: "", opis: "", kategorija: "", stanje: "", slika_url: "" },
  ]);

  const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);
  const [auctionToDeleteId, setAuctionToDeleteId] = useState(null);

  const categories = [
    "Elektronika",
    "Odeca",
    "Umetnost",
    "Sport",
    "Kucni aparati",
    "Namestaj",
    "Vozila",
    "Alati",
    "Kucni ljubimci",
    "Ostalo",
  ];
  const productStates = ["novo", "kao novo", "korisceno", "osteceno"];

  useEffect(() => {
    const fetchMyAuctions = async () => {
      if (!isLoggedIn || !userId) {
        setIsLoading(false);
        return;
      }
      setIsLoading(true);
      setError(null);
      try {
        const authToken = localStorage.getItem("authToken");
        const response = await axios.get(
          `http://localhost:8000/api/korisnici/${userId}/aukcije`,
          {
            headers: {
              Authorization: `Bearer ${authToken}`,
            },
          }
        );
        setMyAuctions(response.data.data);
      } catch (err) {
        console.error("Greska pri ucitavanju mojih aukcija:", err);
        setError("Nije moguce ucitati aukcije.");
      } finally {
        setIsLoading(false);
      }
    };

    if (!loadingAuth) {
      fetchMyAuctions();
    }
  }, [isLoggedIn, userId, triggerAuctionUpdate, loadingAuth]);

  const handleAuctionFormChange = (e) => {
    const { name, value } = e.target;
    setAuctionFormData((prevData) => ({ ...prevData, [name]: value }));
  };

  const handleProductFormChange = (index, e) => {
    const { name, value, files } = e.target;
    const newProductFormsData = [...productFormsData];
    if (name === "slika") {
      newProductFormsData[index] = {
        ...newProductFormsData[index],
        slika: files[0],
      };
    } else {
      newProductFormsData[index] = {
        ...newProductFormsData[index],
        [name]: value,
      };
    }
    setProductFormsData(newProductFormsData);
  };

  const addProductForm = () => {
    setProductFormsData((prevData) => [
      ...prevData,
      { naziv: "", opis: "", kategorija: "", stanje: "", slika_url: "" },
    ]);
  };

  const removeProductForm = (index) => {
    if (productFormsData.length > 1) {
      const newProductFormsData = productFormsData.filter(
        (_, i) => i !== index
      );
      setProductFormsData(newProductFormsData);
    }
  };

  const resetForms = () => {
    setAuctionFormData({
      naziv: "",
      pocetna_cena: "",
      datum_pocetka: "",
      vreme_pocetka: "",
    });
    setProductFormsData([
      { naziv: "", opis: "", kategorija: "", stanje: "", slika_url: "" },
    ]);
  };

  const handleCreateSubmit = async (e) => {
    e.preventDefault();
    setError(null);

    const timeWithSeconds = auctionFormData.vreme_pocetka
      ? `${auctionFormData.vreme_pocetka}:00`
      : "00:00:00";
    const fullDateTime = `${auctionFormData.datum_pocetka} ${timeWithSeconds}`;

    const formData = new FormData();
    formData.append("naziv", auctionFormData.naziv);
    formData.append("pocetna_cena", auctionFormData.pocetna_cena);
    formData.append("datum_pocetka", fullDateTime);

    productFormsData.forEach((product, index) => {
      formData.append(`proizvodi[${index}][naziv]`, product.naziv);
      formData.append(`proizvodi[${index}][opis]`, product.opis);
      formData.append(`proizvodi[${index}][kategorija]`, product.kategorija);
      formData.append(`proizvodi[${index}][stanje]`, product.stanje);
      if (product.slika) {
        formData.append(`proizvodi[${index}][slika]`, product.slika);
      }
    });

    try {
      const authToken = localStorage.getItem("authToken");
      await axios.post("http://localhost:8000/api/aukcije", formData, {
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      });
      setShowCreateModal(false);
      resetForms();
      triggerAuctionUpdate();
    } catch (err) {
      console.error(
        "Greška pri kreiranju aukcije:",
        err.response ? err.response.data : err.message
      );
      setError(
        "Nije moguce kreirati aukciju: " +
          (err.response?.data?.message ||
            JSON.stringify(err.response?.data?.errors) ||
            err.message)
      );
    }
  };

  const handleEditSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    if (!selectedAuction) return;

    const timeWithSeconds = auctionFormData.vreme_pocetka
      ? `${auctionFormData.vreme_pocetka}:00`
      : "00:00:00";
    const fullDateTime = `${auctionFormData.datum_pocetka} ${timeWithSeconds}`;

    try {
      const authToken = localStorage.getItem("authToken");
      const payload = {
        ...auctionFormData,
        datum_pocetka: fullDateTime,
        proizvodi: productFormsData.filter((product) => product.naziv),
      };

      await axios.patch(
        `http://localhost:8000/api/aukcije/${selectedAuction.id}`,
        payload,
        {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        }
      );
      setShowEditModal(false);
      setSelectedAuction(null);
      resetForms();
      triggerAuctionUpdate();
    } catch (err) {
      console.error(
        "Greška pri izmeni aukcije:",
        err.response ? err.response.data : err.message
      );
      setError(
        "Nije moguće izmeniti aukciju: " +
          (err.response?.data?.message ||
            JSON.stringify(err.response?.data?.errors) ||
            err.message)
      );
    }
  };

  const openEditModal = (auction) => {
    setSelectedAuction(auction);

    const auctionDate = new Date(auction.datum_pocetka);
    const datePart = auctionDate.toISOString().split("T")[0]; // YYYY-MM-DD
    const timePart = auctionDate.toTimeString().split(" ")[0].substring(0, 5);

    setAuctionFormData({
      naziv: auction.naziv,
      pocetna_cena: auction.pocetna_cena,
      datum_pocetka: datePart,
      vreme_pocetka: timePart,
    });

    setProductFormsData(
      auction.proizvodi.length > 0
        ? auction.proizvodi.map((p) => ({
            id: p.id,
            naziv: p.naziv,
            opis: p.opis,
            kategorija: p.kategorija,
            stanje: p.stanje,
            slika_url: p.slika_url,
          }))
        : [{ naziv: "", opis: "", kategorija: "", stanje: "", slika_url: "" }]
    );
    setShowEditModal(true);
  };

  const confirmDelete = (auctionId) => {
    setAuctionToDeleteId(auctionId);
    setShowDeleteConfirm(true);
  };

  const handleDelete = async () => {
    if (!auctionToDeleteId) return;
    setError(null);
    try {
      const authToken = localStorage.getItem("authToken");
      await axios.delete(
        `http://localhost:8000/api/aukcije/${auctionToDeleteId}`,
        {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        }
      );
      setShowDeleteConfirm(false);
      setAuctionToDeleteId(null);
      triggerAuctionUpdate();
    } catch (err) {
      console.error(
        "Greška pri brisanju aukcije:",
        err.response ? err.response.data : err.message
      );
      setError(
        "Nije moguce obrisati aukciju: " +
          (err.response?.data?.message || err.message)
      );
    }
  };

  if (loadingAuth || isLoading) {
    return <div className="loading-message">Učitavanje vaših aukcija...</div>;
  }

  if (!isLoggedIn) {
    return (
      <div className="auth-required-message">
        Morate biti prijavljeni da biste videli i upravljali svojim aukcijama.
      </div>
    );
  }

  return (
    <div className="my-auctions-container">
      <h1>Moje Aukcije</h1>

      <button
        className="create-auction-btn"
        onClick={() => {
          resetForms();
          setShowCreateModal(true);
        }}
      >
        Kreiraj Novu Aukciju
      </button>

      {error && <div className="error-message">{error}</div>}

      {myAuctions.length === 0 ? (
        <div className="no-auctions-state">
          Trenutno nemate nijednu aukciju.
        </div>
      ) : (
        <div className="auctions-list">
          {myAuctions.map((auction) => (
            <div key={auction.id} className="auction-item">
              <h3>{auction.naziv}</h3>
              <p>
                Cena: {auction.pocetna_cena} RSD (Trenutna:{" "}
                {auction.trenutna_cena || "N/A"})
              </p>
              <p>Status: {auction.status_aukcije}</p>
              <p>
                Datum početka:{" "}
                {new Date(auction.datum_pocetka).toLocaleString()}
              </p>
              <div className="auction-products">
                {auction.proizvodi && auction.proizvodi.length > 0 && (
                  <>
                    <h4>Proizvodi:</h4>
                    <ul>
                      {auction.proizvodi.map((product) => (
                        <li key={product.id}>
                          {product.naziv} ({product.kategorija},{" "}
                          {product.stanje})
                        </li>
                      ))}
                    </ul>
                  </>
                )}
              </div>
              <div className="auction-actions">
                <button
                  onClick={() => openEditModal(auction)}
                  className="edit-btn"
                >
                  Izmeni
                </button>
                <button
                  onClick={() => confirmDelete(auction.id)}
                  className="delete-btn"
                >
                  Obriši
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {showCreateModal && (
        <div className="modal-overlay">
          <div className="modal-content">
            <h2>Kreiraj Novu Aukciju</h2>
            <form onSubmit={handleCreateSubmit} className="auction-form">
              <h3>Podaci o Aukciji</h3>
              <label>Naziv Aukcije:</label>
              <input
                type="text"
                name="naziv"
                value={auctionFormData.naziv}
                onChange={handleAuctionFormChange}
                required
              />

              <label>Početna Cena:</label>
              <input
                type="number"
                name="pocetna_cena"
                value={auctionFormData.pocetna_cena}
                onChange={handleAuctionFormChange}
                required
                min="0"
              />

              <label>Datum Početka:</label>
              <input
                type="date"
                name="datum_pocetka"
                value={auctionFormData.datum_pocetka}
                onChange={handleAuctionFormChange}
                required
              />

              <label>Vreme Početka:</label>
              <input
                type="time"
                name="vreme_pocetka"
                value={auctionFormData.vreme_pocetka}
                onChange={handleAuctionFormChange}
                required
              />

              <h3>Proizvodi u Aukciji</h3>
              {productFormsData.map((product, index) => (
                <div key={index} className="product-form-group">
                  <h4>Proizvod #{index + 1}</h4>
                  <label>Naziv Proizvoda:</label>
                  <input
                    type="text"
                    name="naziv"
                    value={product.naziv}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  />

                  <label>Opis Proizvoda:</label>
                  <textarea
                    name="opis"
                    value={product.opis}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  />

                  <label>Kategorija:</label>
                  <select
                    name="kategorija"
                    value={product.kategorija}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  >
                    <option value="">Izaberite kategoriju</option>
                    {categories.map((cat) => (
                      <option key={cat} value={cat}>
                        {cat}
                      </option>
                    ))}
                  </select>

                  <label>Stanje:</label>
                  <select
                    name="stanje"
                    value={product.stanje}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  >
                    <option value="">Izaberite stanje</option>
                    {productStates.map((state) => (
                      <option key={state} value={state}>
                        {state}
                      </option>
                    ))}
                  </select>

                  <label>Slika:</label>
                  <input
                    type="file"
                    name="slika"
                    onChange={(e) => handleProductFormChange(index, e)}
                    accept="image/*"
                  />

                  {productFormsData.length > 1 && (
                    <button
                      type="button"
                      onClick={() => removeProductForm(index)}
                      className="remove-product-btn"
                    >
                      Ukloni Proizvod
                    </button>
                  )}
                </div>
              ))}
              <button
                type="button"
                onClick={addProductForm}
                className="add-product-btn"
              >
                Dodaj Još Proizvoda
              </button>

              <div className="modal-actions">
                <button type="submit" className="save-btn">
                  Kreiraj
                </button>
                <button
                  type="button"
                  onClick={() => setShowCreateModal(false)}
                  className="cancel-btn"
                >
                  Poništi
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Izmeni Aukciju Modal */}
      {showEditModal && selectedAuction && (
        <div className="modal-overlay">
          <div className="modal-content">
            <h2>Izmeni Aukciju: {selectedAuction.naziv}</h2>
            <form onSubmit={handleEditSubmit} className="auction-form">
              <h3>Podaci o Aukciji</h3>
              <label>Naziv Aukcije:</label>
              <input
                type="text"
                name="naziv"
                value={auctionFormData.naziv}
                onChange={handleAuctionFormChange}
                required
              />

              <label>Početna Cena:</label>
              <input
                type="number"
                name="pocetna_cena"
                value={auctionFormData.pocetna_cena}
                onChange={handleAuctionFormChange}
                required
                min="0"
              />

              <label>Datum Početka:</label>
              <input
                type="date"
                name="datum_pocetka"
                value={auctionFormData.datum_pocetka}
                onChange={handleAuctionFormChange}
                required
              />

              <label>Vreme Početka:</label>
              <input
                type="time"
                name="vreme_pocetka"
                value={auctionFormData.vreme_pocetka}
                onChange={handleAuctionFormChange}
                required
              />

              <h3>Proizvodi u Aukciji</h3>
              {productFormsData.map((product, index) => (
                <div key={index} className="product-form-group">
                  <h4>Proizvod #{index + 1}</h4>
                  <label>Naziv Proizvoda:</label>
                  <input
                    type="text"
                    name="naziv"
                    value={product.naziv}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  />

                  <label>Opis Proizvoda:</label>
                  <textarea
                    name="opis"
                    value={product.opis}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  />

                  <label>Kategorija:</label>
                  <select
                    name="kategorija"
                    value={product.kategorija}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  >
                    <option value="">Izaberite kategoriju</option>
                    {categories.map((cat) => (
                      <option key={cat} value={cat}>
                        {cat}
                      </option>
                    ))}
                  </select>

                  <label>Stanje:</label>
                  <select
                    name="stanje"
                    value={product.stanje}
                    onChange={(e) => handleProductFormChange(index, e)}
                    required
                  >
                    <option value="">Izaberite stanje</option>
                    {productStates.map((state) => (
                      <option key={state} value={state}>
                        {state}
                      </option>
                    ))}
                  </select>

                  <label>Slika:</label>
                  <input
                    type="file"
                    name="slika"
                    onChange={(e) => handleProductFormChange(index, e)}
                    accept="image/*"
                  />

                  {productFormsData.length > 1 && (
                    <button
                      type="button"
                      onClick={() => removeProductForm(index)}
                      className="remove-product-btn"
                    >
                      Ukloni Proizvod
                    </button>
                  )}
                </div>
              ))}
              <button
                type="button"
                onClick={addProductForm}
                className="add-product-btn"
              >
                Dodaj Još Proizvoda
              </button>

              <div className="modal-actions">
                <button type="submit" className="save-btn">
                  Sačuvaj Izmene
                </button>
                <button
                  type="button"
                  onClick={() => setShowEditModal(false)}
                  className="cancel-btn"
                >
                  Poništi
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Potvrda brisanja Modal */}
      {showDeleteConfirm && (
        <div className="modal-overlay">
          <div className="modal-content small-modal">
            <h2>Potvrdi Brisanje</h2>
            <p>Da li ste sigurni da želite da obrišete ovu aukciju?</p>
            <div className="modal-actions">
              <button onClick={handleDelete} className="delete-btn">
                Obriši
              </button>
              <button
                onClick={() => setShowDeleteConfirm(false)}
                className="cancel-btn"
              >
                Poništi
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default MyAuctions;

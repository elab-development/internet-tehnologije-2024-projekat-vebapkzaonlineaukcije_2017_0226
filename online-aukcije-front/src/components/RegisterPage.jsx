import React, { useState } from "react";
import axios from "axios";

const RegisterPage = () => {
  const [formData, setFormData] = useState({
    ime: "",
    prezime: "",
    email: "",
    password: "",
    broj_telefona: "",
    adresa: "",
    stanje_na_racunu: "",
  });

  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevState) => ({
      ...prevState,
      [name]: value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    setError(null);
    setSuccess(false);

    try {
      const response = await axios.post(
        "http://localhost:8000/api/register",
        formData
      );

      console.log("Registracija uspesna:", response.data);
      setSuccess(true);
    } catch (err) {
      console.error(
        "Doslo je do greske prilikom registracije:",
        err.response.data
      );
      setError(
        err.response.data.message || "Doslo je do greske prilikom registracije."
      );
    }
  };

  return (
    <div className="register-container">
      <h2>Registrujte se</h2>
      {error && <div className="error-message">{error}</div>}
      {success && (
        <div className="success-message">Uspesno ste se registrovali!</div>
      )}
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Ime:</label>
          <input
            type="text"
            name="ime"
            value={formData.ime}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Prezime:</label>
          <input
            type="text"
            name="prezime"
            value={formData.prezime}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Email:</label>
          <input
            type="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Lozinka:</label>
          <input
            type="password"
            name="password"
            value={formData.lozinka}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Broj telefona:</label>
          <input
            type="tel"
            name="broj_telefona"
            value={formData.broj_telefona}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Adresa:</label>
          <input
            type="text"
            name="adresa"
            value={formData.adresa}
            onChange={handleChange}
            required
          />
        </div>

        <div className="form-group">
          <label>Stanje na racunu:</label>
          <input
            type="number"
            name="stanje_na_racunu"
            value={formData.stanje_na_racunu}
            onChange={handleChange}
            required
          />
        </div>

        <button type="submit">Registruj se</button>
      </form>
    </div>
  );
};

export default RegisterPage;

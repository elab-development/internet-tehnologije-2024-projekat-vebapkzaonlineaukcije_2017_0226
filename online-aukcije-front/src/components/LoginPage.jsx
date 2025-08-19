import React, { useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

const LoginPage = () => {
  const [formData, setFormData] = useState({
    email: "",
    password: "",
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
        "http://localhost:8000/api/login",
        formData
      );

      console.log("Ulogovanje uspesno:", response.data);
      setSuccess(true);
    } catch (err) {
      console.error(
        "Doslo je do greske prilikom logovanja:",
        err.response.data
      );
      setError(
        err.response.data.message || "Doslo je do greske prilikom logovanja."
      );
    }
  };

  return (
    <div className="register-container">
      <h2>Ulogujte se</h2>
      {error && <div className="error-message">{error}</div>}
      {success && (
        <div className="success-message">Uspesno ste se ulogovali!</div>
      )}
      <form onSubmit={handleSubmit}>
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

        <button type="submit">Uloguj se</button>

        <p className="register-link">
          Nemate nalog? &nbsp;<Link to="/register"> Registrujte se ovde</Link>
        </p>
      </form>
    </div>
  );
};

export default LoginPage;

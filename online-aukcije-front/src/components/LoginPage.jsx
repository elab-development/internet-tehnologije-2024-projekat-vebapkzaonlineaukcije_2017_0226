import React, { useState, useContext } from "react";
import axios from "axios";
import { Link, useNavigate } from "react-router-dom";
import { AuthContext } from "../context/AuthContext";

const LoginPage = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  const navigate = useNavigate();
  const { login } = useContext(AuthContext);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      const response = await axios.post("http://localhost:8000/api/login", {
        email,
        password,
      });

      const authToken = response.data.access_token;
      const user = response.data.data;

      axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`;

      login(authToken, user);

      console.log("Prijava uspesna:", response.data);
      navigate("/login");
    } catch (err) {
      console.error(
        "Doslo je do greske prilikom prijave:",
        err.response ? err.response.data : err.message
      );
      setError(err.response?.data?.message || "Neispravan email ili lozinka.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="register-container">
      <h2>Prijavi se</h2>
      {error && <div className="error-message">{error}</div>}
      <form onSubmit={handleSubmit} className="login-form">
        <div className="form-group">
          <label htmlFor="email">Email:</label>
          <input
            type="email"
            id="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="password">Lozinka:</label>
          <input
            type="password"
            id="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>

        <button type="submit" disabled={loading}>
          {loading ? "Prijavljivanje..." : "Prijavi se"}
        </button>
      </form>

      <p className="register-prompt">
        Nemas nalog? <Link to="/register">Registruj se ovde</Link>
      </p>
    </div>
  );
};

export default LoginPage;

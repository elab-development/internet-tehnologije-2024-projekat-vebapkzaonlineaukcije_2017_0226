import React, { useContext } from "react";
import { Link, useNavigate } from "react-router-dom";
import axios from "axios";
import { AuthContext } from "../context/AuthContext";

const Navbar = () => {
  const { isLoggedIn, firstName, lastName, logout, loadingAuth } =
    useContext(AuthContext);
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  if (loadingAuth) {
    return (
      <nav className="navbar">
        <div className="navbar-logo">
          <Link to="/">
            <img
              src="/logo3.png"
              alt="Online aukcije logo"
              className="logo-img"
            />
          </Link>
        </div>
        <div className="navbar-auth">
          <span>Ucitavanje...</span>
        </div>
      </nav>
    );
  }

  return (
    <nav className="navbar">
      <div className="navbar-logo">
        <Link to="/">
          <img
            src="/logo3.png"
            alt="Online aukcije logo"
            className="logo-img"
          />
        </Link>
      </div>

      <h1 className="navbar-title">Online aukcije</h1>

      <ul className="navbar-links">
        {isLoggedIn && (
          <>
            <li>
              <Link to="/moje-aukcije">Moje aukcije</Link>
            </li>
            <li>
              <Link to="/moj-profil">Moj profil</Link>
            </li>
            <li>
              <Link to="/obavestenja">Obaveštenja</Link>
            </li>
          </>
        )}
      </ul>

      <div className="navbar-auth">
        {isLoggedIn ? (
          <>
            <span className="welcome-message">
              Zdravo, {firstName} {lastName}!
            </span>
            <button onClick={handleLogout} className="logout-button">
              Odjavi se
            </button>
          </>
        ) : (
          <>
            <Link to="/login" className="auth-link">
              Prijavi se
            </Link>
            <Link to="/register" className="auth-link">
              Registruj se
            </Link>
          </>
        )}
      </div>
    </nav>
  );
};

export default Navbar;

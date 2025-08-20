import React, { useState, useEffect, useContext } from "react";
import axios from "axios";
import { AuthContext } from "../context/AuthContext";

const MyProfile = () => {
  const { isLoggedIn } = useContext(AuthContext);
  const [userData, setUserData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [isEditing, setIsEditing] = useState(false);
  const [formData, setFormData] = useState({});

  useEffect(() => {
    const fetchUserData = async () => {
      setLoading(true);
      setError(null);
      try {
        const authToken = localStorage.getItem("authToken");
        if (!authToken) {
          throw new Error("Korisnik nije prijavljen.");
        }

        const response = await axios.get("http://localhost:8000/api/korisnik", {
          headers: {
            Authorization: `Bearer ${authToken}`,
          },
        });

        setUserData(response.data);
        setFormData(response.data);
      } catch (err) {
        console.error("Greska pri citanju podataka:", err);
        setError("Nismo uspeli da ucitamo podatke. Pokusajte ponovo.");
      } finally {
        setLoading(false);
      }
    };

    if (isLoggedIn) {
      fetchUserData();
    }
  }, [isLoggedIn]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
  };

  const handleSave = async () => {
    try {
      setLoading(true);
      const authToken = localStorage.getItem("authToken");
      await axios.patch("http://localhost:8000/api/korisnik", formData, {
        headers: {
          Authorization: `Bearer ${authToken}`,
        },
      });

      setUserData(formData);
      setIsEditing(false);
      alert("Podaci su uspesno azurirani.");
    } catch (err) {
      console.error("Greska pri azuriranju:", err.response.data);
      setError("Neuspelo azuriranje podataka.");
    } finally {
      setLoading(false);
    }
  };

  if (!isLoggedIn) {
    return (
      <div className="profile-container">
        <h2>Pristup zabranjen</h2>
        <p>Morate biti prijavljeni da biste videli svoj profil.</p>
      </div>
    );
  }

  if (loading) {
    return <div className="profile-container">Učitavanje profila...</div>;
  }

  if (error) {
    return <div className="profile-container error-message">{error}</div>;
  }

  return (
    <div className="profile-container">
      <h2>Moj profil</h2>
      <div className="profile-details">
        {isEditing ? (
          <>
            <p>
              <strong>Adresa:</strong>
              <input
                type="text"
                name="adresa"
                value={formData.adresa || ""}
                onChange={handleInputChange}
              />
            </p>
            <p>
              <strong>Broj telefona:</strong>
              <input
                type="text"
                name="broj_telefona"
                value={formData.broj_telefona || ""}
                onChange={handleInputChange}
              />
            </p>
            <p>
              <strong>Stanje na računu:</strong>
              <input
                type="number"
                name="stanje_na_racunu"
                value={formData.stanje_na_racunu || ""}
                onChange={handleInputChange}
              />
            </p>
            <p>
              <strong>Ime i prezime:</strong> {userData.ime} {userData.prezime}
            </p>
            <p>
              <strong>Email:</strong> {userData.email}
            </p>
          </>
        ) : (
          <>
            <p>
              <strong>Ime i prezime:</strong> {userData.ime} {userData.prezime}
            </p>
            <p>
              <strong>Email:</strong> {userData.email}
            </p>
            <p>
              <strong>Adresa:</strong> {userData.adresa}
            </p>
            <p>
              <strong>Broj telefona:</strong> {userData.broj_telefona}
            </p>
            <p>
              <strong>Stanje na računu:</strong> {userData.stanje_na_racunu} RSD
            </p>
          </>
        )}
      </div>
      <div className="profile-actions">
        {isEditing ? (
          <button onClick={handleSave} disabled={loading}>
            Sacuvaj promene
          </button>
        ) : (
          <button onClick={() => setIsEditing(true)}>Izmeni podatke</button>
        )}
      </div>
    </div>
  );
};

export default MyProfile;

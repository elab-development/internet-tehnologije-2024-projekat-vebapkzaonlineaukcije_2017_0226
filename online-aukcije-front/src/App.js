import logo from "./logo.svg";
import "./App.css";
import HomePage from "./components/HomePage";
import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import RegisterPage from "./components/RegisterPage";
import LoginPage from "./components/LoginPage";
import Navbar from "./components/Navbar";
import { AuthProvider } from "./context/AuthContext";
import MyProfile from "./components/MyProfile";
import MyAuctions from "./components/MyAuctions";
import AukcijaDetailsPage from "./components/AuctionDetailsPage";
import Notifications from "./components/Notifications";

function App() {
  return (
    <AuthProvider>
      <BrowserRouter className="App">
        <Navbar />
        <main>
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/login" element={<LoginPage />} />
            <Route path="/moj-profil" element={<MyProfile />} />
            <Route path="/moje-aukcije" element={<MyAuctions />} />
            <Route path="/aukcija/:id" element={<AukcijaDetailsPage />} />
            <Route path="/obavestenja" element={<Notifications />} />
          </Routes>
        </main>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;

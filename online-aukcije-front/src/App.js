import logo from "./logo.svg";
import "./App.css";
import HomePage from "./components/HomePage";
import { BrowserRouter, Routes, Route, Link } from "react-router-dom";
import RegisterPage from "./components/RegisterPage";
import LoginPage from "./components/LoginPage";
import Navbar from "./components/Navbar";
import { AuthProvider } from "./context/AuthContext";
import MyProfile from "./components/MyProfile";

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
          </Routes>
        </main>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;

import React from "react";

const AuctionCard = ({ auction }) => {
  const {
    naziv,
    pocetna_cena,
    trenutna_cena,
    maksimalna_cena,
    datum_pocetka,
    vreme_isteka,
    status_aukcije,
    proizvodi,
  } = auction;
  const prvaSlika =
    proizvodi && proizvodi.length > 0
      ? proizvodi[0].slika_url
      : "https://via.placeholder.com/300x200";

  return (
    <div className="auction-card">
      <div className="card-image-container">
        <img src={prvaSlika} alt={`Slika za aukciju: ${naziv}`} />
      </div>
      <div className="card-content">
        <h3 className="card-title">{naziv}</h3>
        <p>
          <strong>Trenutna ponuda:</strong> {trenutna_cena} RSD
        </p>
        <p>
          <strong>Ističe:</strong> {new Date(vreme_isteka).toLocaleString()}
        </p>
        <button className="view-button">Pogledaj aukciju</button>
      </div>
    </div>
  );
};

export default AuctionCard;

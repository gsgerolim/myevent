-- Seed sample data (5 events + 2 ads)
INSERT INTO events (name, responsible, summary, address, latitude, longitude, date_start, date_end, capacity, unlimited, image, status, category, cost, link)
VALUES
('Feira Tech Ilha', 'Nadia', 'Feira de tecnologias e projetos locais', 'Praça Central, Ilha Solteira', -20.4244, -51.3639, '2025-09-20 09:00', '2025-09-20 17:00', 200, false, 'assets/uploads/sample1.jpg', 'active', 'Feira', 'Gratuito', ''),
('Palestra IoT', 'Prof. Virgilio', 'Palestra sobre IoT e aplicações', 'Auditório Municipal', -20.4270, -51.3650, '2025-09-21 14:00', '2025-09-21 16:00', 80, false, 'assets/uploads/sample2.jpg', 'active', 'Palestra', 'R$20', ''),
('Workshop Impressão 3D', 'Equipe Mtec', 'Transformação de PET em filamento', 'Laboratório de Inovação', -20.4250, -51.3640, '2025-09-22 10:00', '2025-09-22 15:00', 30, false, 'assets/uploads/sample3.jpg', 'active', 'Workshop', 'R$50', ''),
('Hackathon Jovem', 'Coordenação', 'Maratona de programação 24h', 'Centro de Convenções', -20.4260, -51.3660, '2025-09-25 10:00', '2025-09-26 10:00', 100, false, 'assets/uploads/sample4.jpg', 'active', 'Hackathon', 'Inscrição', ''),
('Exposição Sustentável', 'Organização', 'Produtos ecologicamente corretos', 'Galeria Cultural', -20.4280, -51.3620, '2025-09-23 09:00', '2025-09-23 18:00', NULL, true, 'assets/uploads/sample5.jpg', 'active', 'Exposição', 'Gratuito', '');

INSERT INTO ads (title, image, link, date_start, date_end, display_time, status)
VALUES
('Patrocínio XYZ', 'assets/uploads/ad1.jpg', 'https://patrocinador.example.com', '2025-09-01', '2025-10-01', 7, 'active'),
('Apoio Local', 'assets/uploads/ad2.jpg', 'https://apoio.example.com', '2025-09-10', '2025-09-30', 5, 'active');


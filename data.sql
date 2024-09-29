USE [roemah54]
GO
/****** Object:  Table [dbo].[menus]    Script Date: 09/29/2024 15:10:09 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[menus](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[route] [nvarchar](255) NULL,
	[parent_id] [bigint] NULL,
	[order] [int] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[menus] ON
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (1, N'Kamar', NULL, NULL, 1, CAST(0x0000B1FA00B55CE3 AS DateTime), CAST(0x0000B1FA00B55CE3 AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (2, N'Daftar Kamar', N'kamar', 1, 1, CAST(0x0000B1FA00B55CEC AS DateTime), CAST(0x0000B1FA00B55CEC AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (3, N'Harga Kamar', N'harga', 1, 2, CAST(0x0000B1FA00B55CEC AS DateTime), CAST(0x0000B1FA00B55CEC AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (4, N'Penyewa', NULL, NULL, 2, CAST(0x0000B1FA00B55CEC AS DateTime), CAST(0x0000B1FA00B55CEC AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (5, N'Daftar Penyewa', N'daftarpenyewa', 4, 1, CAST(0x0000B1FA00B55CEC AS DateTime), CAST(0x0000B1FA00B55CEC AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (6, N'Penyewaan Kamar', N'penyewaankamar', 4, 2, CAST(0x0000B1FA00B55CED AS DateTime), CAST(0x0000B1FA00B55CED AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (7, N'Laporan', NULL, NULL, 3, CAST(0x0000B1FA00B55CED AS DateTime), CAST(0x0000B1FA00B55CED AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (8, N'Transaksi', N'transaksi', 7, 1, CAST(0x0000B1FA00B55CED AS DateTime), CAST(0x0000B1FA00B55CED AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (9, N'Manajemen Pengguna', NULL, NULL, 4, CAST(0x0000B1FA00B55CEE AS DateTime), CAST(0x0000B1FA00B55CEE AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (10, N'Role', N'role', 9, 1, CAST(0x0000B1FA00B55CEE AS DateTime), CAST(0x0000B1FA00B55CEE AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (11, N'Pengguna', N'pengguna', 9, 2, CAST(0x0000B1FA00B55CEE AS DateTime), CAST(0x0000B1FA00B55CEE AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (12, N'Manajemen Menu', NULL, NULL, 5, CAST(0x0000B1FA00B55CEE AS DateTime), CAST(0x0000B1FA00B55CEE AS DateTime))
INSERT [dbo].[menus] ([id], [name], [route], [parent_id], [order], [created_at], [updated_at]) VALUES (13, N'Menu', N'menu', 12, 1, CAST(0x0000B1FA00B55CEE AS DateTime), CAST(0x0000B1FA00B55CEE AS DateTime))
SET IDENTITY_INSERT [dbo].[menus] OFF
/****** Object:  Table [dbo].[menuroles]    Script Date: 09/29/2024 15:10:09 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[menuroles](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[menu_id] [bigint] NOT NULL,
	[role_id] [bigint] NOT NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[menuroles] ON
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (1, 1, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (2, 1, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (3, 1, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (4, 1, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (5, 2, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (6, 2, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (7, 2, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (8, 2, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (9, 3, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (10, 3, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (11, 3, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (12, 3, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (13, 4, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (14, 4, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (15, 4, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (16, 4, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (17, 5, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (18, 5, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (19, 5, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (20, 5, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (21, 6, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (22, 6, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (23, 6, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (24, 6, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (25, 7, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (26, 7, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (27, 7, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (28, 7, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (29, 8, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (30, 8, 2, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (31, 8, 3, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (32, 8, 4, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (33, 9, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (34, 10, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (36, 11, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (37, 12, 1, NULL, NULL)
INSERT [dbo].[menuroles] ([id], [menu_id], [role_id], [created_at], [updated_at]) VALUES (38, 13, 1, NULL, NULL)
SET IDENTITY_INSERT [dbo].[menuroles] OFF
/****** Object:  Default [DF__menus__order__27B9C2CD]    Script Date: 09/29/2024 15:10:09 ******/
ALTER TABLE [dbo].[menus] ADD  DEFAULT ('0') FOR [order]
GO
